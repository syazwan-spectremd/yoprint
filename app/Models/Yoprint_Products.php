<?php

namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Yoprint_Products
{
    // use HasFactory;
    // protected $table = 'yoprint_products';
    // protected $primaryKey = 'yp_id';
    // public $incrementing = false;

    // protected $fillable = [
    //     "name",
    //     "details",
    //     "unique_key",
    //     "product_title",
    //     "product_description",
    //     "style",
    //     "sanmar_mainframe_color",
    //     "size",
    //     "color_name",
    //     "piece_price"
    // ];

    public function insertDataFromCSV($data){
        $column = [
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
        $query = DB::table('yoprint_products')->upsert($data, $column, $data);

        if ($query){
            return true;
        }
        else{
            return false;
        }
    }

    public function insertUploadedFilename($data){
        $query = DB::table('files_uploaded')->insertGetId($data);

        if ($query){
            return $query;
        }
        else{
            return false;
        }
    }

    public function getFileList(){
        $products = DB::table('files_uploaded')
                        ->select("*")
                        ->get();

        return $products;
    }
}
