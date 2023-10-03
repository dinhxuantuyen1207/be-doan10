<?php

use App\Http\Controllers\DongSanPhamController;
use App\Http\Controllers\NguoiDungController;
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
});

