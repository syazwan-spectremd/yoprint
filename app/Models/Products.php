<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Products extends Model
{
    use HasFactory;

    protected $fillable = [
        "name",
        "details",
        "unique_key",
        "product_title",
        "product_description",
        "style",
        "sanmar_mainframe_color",
        "size",
        "color_name",
        "piece_price"
    ];

    public function getProductDetails($product_id){
        $products = DB::table('yoprint_products')
                        ->select("*")
                        ->where("product_id", $product_id)
                        ->first();

        return $products;
    }

    public function updateProductDetails($product_id, $productDetails){
        $query = DB::table('products')
                        ->where("product_id", $product_id)
                        ->update($productDetails);

        if ($query){
            return true;
        }
        else{
            return false;
        }
    }

    public function deleteProductsFromList($product_id){
        $query = DB::table('products')
                        ->where("product_id", $product_id)
                        ->delete();
        if ($query){
            return true;
        }
        else{
            return false;
        }
    }

    public function insertDataFromCSV($data){
        $query = DB::table('yoprint_products')->insert($data);

        if ($query){
            return true;
        }
        else{
            return false;
        }
    }

    public function getProductList(){
        $query = DB::table("yoprint_products")
                        ->select("*")
                        ->get();
        if ($query){
            return $query->toArray();
        }
        else{
            return false;
        }
    }
}
