<?php

namespace App\Http\Controllers;

use App\Models\Products;
use App\Models\Yoprint_Products;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Input\Input;
use Illuminate\Support\Facades\Response;
use App\Jobs\UploadCSVDataJob;
use Illuminate\Support\Facades\Bus;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    protected $products;

    public function __construct()
    {
        $this->products = new Products();
        $this->yoprint = new Yoprint_Products();
    }
    public function index(Request $request)
    {
        $filelist = $this->yoprint->getFileList();
        return view("products.index", compact("filelist"))
            ->with("i", (request()->input("page", 1) - 1) * 5);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view("products.create");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            "name"      => "required",
            "details"   => "required"
        ]);

        Products::create($request->all());

        return redirect()->route("products.index")
            ->with("success", "Product created successfully");
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Products  $products
     * @return \Illuminate\Http\Response
     */
    public function show($product_id)
    {
        $productDetails = $this->products->getProductDetails($product_id);
        return view("products.show", array("products" => $productDetails));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Products  $products
     * @return \Illuminate\Http\Response
     */
    public function edit($product_id)
    {
        $productDetails = $this->products->getProductDetails($product_id);
        return view("products.edit", ["products" => $productDetails]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Products  $products
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $product_id)
    {
        $request->validate([
            "name"      => "required",
            "details"   => "required"
        ]);
        var_dump($request["name"]);
        $details = array(
            "name"  => $request["name"],
            "details" => $request["details"]
        );
        $updateDetails = $this->products->updateProductDetails($product_id, $details);

        if ($updateDetails) {
            return redirect()->route("products.index")
                ->with("success", "Successfully updated product description");
        } else {
            return redirect()->route("products.index")
                ->with("failed", "Failed to update data");
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Products  $products
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $post = $request->input("product_id");
        $deleteProduct = $this->products->deleteProductsFromList($post);

        if ($deleteProduct) {
            $data["valid"] = true;
            $data["message"] = "Successfully delete product";
        } else {
            $data["valid"] = false;
            $data["message"] = "Failed to delete product";
        }

        return Response::json($data);
    }

    public function importCSV(Request $request)
    {
        $request->validate([
            'csvFile' => 'required|mimes:csv',
        ]);        
        $csvFile = $request->file("csvFile");
        $path = $csvFile->getRealPath();
        $filename = $csvFile->getClientOriginalName();
        if (file_exists($path) && is_readable($path)) {
            $records = array_map("str_getcsv", file($path));
        } else {
            return redirect()->route("products.index")
                ->with("Error: Unable to open the file or file does not exist.");
        }

        if (!count($records) > 0) {
            return "error";
        }
        // Get field names from header column
        $fields = array_map("strtolower", $records[0]);
        
        // Remove the header column
        $csvData = array_shift($records);

        $dataFiles = array(
            "fu_name" => $filename,
            "fu_status" => 1 //initially in progress
        );
        $insertUploadedFilename = $this->yoprint->insertUploadedFilename($dataFiles);
        
        $jobs = [];
        foreach ($records as $record) {
            if (count($fields) != count($record)) {
                return "csv_upload_invalid_data";
            }

            // Decode unwanted html entities
            $record = array_map("html_entity_decode", $record);

            //Set the field name
            $record = array_combine($fields, $record);

            //get clean data
            $cleanRecord = $this->clear_encoding_str($record);
            $cleanRecord["fu_id"] = $insertUploadedFilename;
            $dataClean[] = $cleanRecord;
            // $jobs[] = (new UploadCSVDataJob($dataClean))->onQueue("default");
        }

        try {
            // $processing = UploadCSVDataJob::dispatch($dataClean);
            $processing = UploadCSVDataJob::dispatch($dataClean)->onQueue("default");
            // pr($processing);
            // $processing =  Bus::batch($jobs)->dispatch();
        } catch (\Throwable $th) {
            $data["valid"] = false;
            $data["message"] = $th;
        }  

        if ($processing && $insertUploadedFilename) {
            // pr($processing->id);
            return redirect()->route("products.index")
                ->with("success", "Successfully updated product description");
        } else {
            return redirect()->route("products.index")
                ->with("failed", "Failed");
        }
    }

    private function clear_encoding_str($value)
    {
        if (is_array($value)) {
            $clean = [];
            foreach ($value as $key => $val) {
                $clean[$key] = mb_convert_encoding($val, 'UTF-8', 'UTF-8');
            }
            return $clean;
        }
        return mb_convert_encoding($value, 'UTF-8', 'UTF-8');
    }
}
