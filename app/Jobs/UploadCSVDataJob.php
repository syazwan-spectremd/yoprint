<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Bus\Batchable;
use App\Models\Yoprint_Products;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class UploadCSVDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;

    protected $csvData;
    public $products;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($csvData)
    {
        $this->csvData = $csvData;
        $this->products = new Yoprint_Products();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {   
        foreach ($this->csvData as $value) {
            $csvData = array(
                "fu_id" => $value["fu_id"],
                "unique_key"  => $value["unique_key"],
                "product_title" => $value["product_title"],
                "product_description"  => $value["product_description"],
                "style" => $value["style#"],
                "sanmar_mainframe_color" => $value["sanmar_mainframe_color"],
                "size"  => $value["size"],
                "color_name"    => $value["color_name"],
                "piece_price"   => $value["piece_price"]
            );            
            $insertData = $this->products->insertDataFromCSV($csvData);
            DB::table('files_uploaded')->where('fu_id', $value["fu_id"])->update(['fu_status' => 2]);
        }
        // Log::info('Handling UploadCSVDataJob', ['data' => $this->data]);
    }
}
