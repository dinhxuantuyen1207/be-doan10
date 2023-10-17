<?php

use App\Http\Controllers\ChuDeFAQController;
use App\Http\Controllers\DongSanPhamController;
use App\Http\Controllers\GioHangController;
use App\Http\Controllers\LoaiSanPhamController;
use App\Http\Controllers\NguoiDungController;
use App\Http\Controllers\SanPhamController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::prefix('/user')->group(function () {
    Route::get('/list',[NguoiDungController::class,'list']);
    Route::post('/profile',[NguoiDungController::class,'profile']);
    Route::post('/update',[NguoiDungController::class,'update']);
    Route::post('/change-password',[NguoiDungController::class,'changePassword']);
    Route::post('/destroy',[NguoiDungController::class,'destroy']);
    Route::post('/create',[NguoiDungController::class,'create']);
    Route::post('/login',[NguoiDungController::class,'login']);
});

Route::prefix('/product-types')->group(function () {
    Route::post('/create',[DongSanPhamController::class,'create']);
    Route::post('/destroy',[DongSanPhamController::class,'destroy']);
    Route::post('/edit',[DongSanPhamController::class,'edit']);
    Route::get('/list',[DongSanPhamController::class,'list']);

});

Route::prefix('/category-types')->group(function () {
    Route::post('/create',[LoaiSanPhamController::class,'create']);
    Route::post('/destroy',[LoaiSanPhamController::class,'destroy']);
    Route::post('/edit',[LoaiSanPhamController::class,'edit']);
    Route::get('/list',[LoaiSanPhamController::class,'list']);
});

Route::prefix('/product')->group(function () {
    Route::post('/create',[SanPhamController::class,'create']);
    Route::get('/{id}/edit',[SanPhamController::class,'edit']);
    Route::post('/update',[SanPhamController::class,'update']);
    Route::post('/detail',[SanPhamController::class,'detail']);
    Route::post('/list',[SanPhamController::class,'list']);
    Route::post('/list-all',[SanPhamController::class,'listAll']);
    Route::post('/destroy',[SanPhamController::class,'destroy']);
    // Route::post('/filter',[SanPhamController::class,'filter']);
});

Route::prefix('/cart')->group(function () {
    Route::post('/add-to-cart',[GioHangController::class,'addToCart']);
    Route::get('/{id}/edit',[SanPhamController::class,'edit']);
    Route::get('/list',[SanPhamController::class,'list']);
    Route::post('/detail',[SanPhamController::class,'detail']);
});

Route::prefix('/faq')->group(function () {
    Route::get('/list',[ChuDeFAQController::class,'list']);
});
