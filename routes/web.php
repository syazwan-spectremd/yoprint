<?php

use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get("/products", ProductController::class . "@index")->name("products.index");
Route::get("/products/create", ProductController::class . "@create")->name("products.create");
Route::post("/products/store", ProductController::class . "@store")->name("products.store");
Route::post("/products/destroy", ProductController::class . "@destroy")->name("products.destroy");
Route::get("/products/show/{product}", ProductController::class . "@show")->name("products.show");
Route::get("/products/{product_id}/edit", ProductController::class . "@edit")->name("products.edit");
Route::put("/products/update/{product_id}", ProductController::class . "@update")->name("products.update");
Route::post("/products/importCSV", ProductController::class . "@importCSV")->name("products.importCSV");