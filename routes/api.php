<?php

use App\Http\Controllers\BangLuongController;
use App\Http\Controllers\ChiTietGioHangController;
use App\Http\Controllers\ChucVuController;
use App\Http\Controllers\ChuDeFAQController;
use App\Http\Controllers\DongSanPhamController;
use App\Http\Controllers\FAQController;
use App\Http\Controllers\GioHangController;
use App\Http\Controllers\HoaDonController;
use App\Http\Controllers\KhoController;
use App\Http\Controllers\LoaiSanPhamController;
use App\Http\Controllers\NguoiDungController;
use App\Http\Controllers\NhanVienController;
use App\Http\Controllers\QuyenHanController;
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
    Route::get('/list', [NguoiDungController::class, 'list']);
    Route::post('/list-all', [NguoiDungController::class, 'listAll']);
    Route::post('/profile', [NguoiDungController::class, 'profile']);
    Route::post('/update', [NguoiDungController::class, 'update']);
    Route::post('/change-password', [NguoiDungController::class, 'changePassword']);
    Route::post('/destroy', [NguoiDungController::class, 'destroy']);
    Route::post('/create', [NguoiDungController::class, 'create']);
    Route::post('/login', [NguoiDungController::class, 'login']);
});

Route::prefix('/product-types')->group(function () {
    Route::post('/create', [DongSanPhamController::class, 'create']);
    Route::post('/destroy', [DongSanPhamController::class, 'destroy']);
    Route::post('/edit', [DongSanPhamController::class, 'edit']);
    Route::get('/list', [DongSanPhamController::class, 'list']);
});

Route::prefix('/category-types')->group(function () {
    Route::post('/create', [LoaiSanPhamController::class, 'create']);
    Route::post('/destroy', [LoaiSanPhamController::class, 'destroy']);
    Route::post('/edit', [LoaiSanPhamController::class, 'edit']);
    Route::get('/list', [LoaiSanPhamController::class, 'list']);
    Route::post('/list-id', [LoaiSanPhamController::class, 'listId']);
});

Route::prefix('/product')->group(function () {
    Route::post('/create', [SanPhamController::class, 'create']);
    Route::get('/{id}/edit', [SanPhamController::class, 'edit']);
    Route::post('/update', [SanPhamController::class, 'update']);
    Route::post('/detail', [SanPhamController::class, 'detail']);
    Route::post('/list', [SanPhamController::class, 'list']);
    Route::post('/list-all', [SanPhamController::class, 'listAll']);
    Route::post('/destroy', [SanPhamController::class, 'destroy']);
    // Route::post('/filter',[SanPhamController::class,'filter']);
    Route::get('/{id}', [SanPhamController::class, 'productDetail']);
    Route::post('/search', [SanPhamController::class, 'search']);
    Route::post('/list-id', [SanPhamController::class, 'listId']);
});

Route::prefix('/cart')->group(function () {
    Route::post('/add-to-cart', [GioHangController::class, 'addToCart']);
    Route::post('/my-cart', [GioHangController::class, 'myCart']);
    Route::post('/change-cart', [ChiTietGioHangController::class, 'changeCart']);
    Route::post('/get-payment-amount', [GioHangController::class, 'getPaymentAmount']);
    Route::post('/create-qr-code-zalo', [GioHangController::class, 'createQRCodeZalo']);
});

Route::prefix('/faq')->group(function () {
    Route::get('/list', [ChuDeFAQController::class, 'list']);
    Route::post('/list-faq', [FAQController::class, 'listFAQ']);
    Route::post('/create', [FAQController::class, 'create']);
    Route::post('/update', [FAQController::class, 'update']);
    Route::post('/destroy', [FAQController::class, 'destroy']);
});

Route::prefix('/chu-de-faq')->group(function () {
    Route::get('/list', [ChuDeFAQController::class, 'listTitle']);
    Route::post('/create', [ChuDeFAQController::class, 'create']);
    Route::post('/destroy', [ChuDeFAQController::class, 'destroy']);
});

Route::prefix('/hoa-don')->group(function () {
    Route::get('/hoa-don-list-user/{id}', [HoaDonController::class, 'hoaDonListUser']);
    Route::post('/list-all', [HoaDonController::class, 'listAll']);
    Route::get('/trang-thai/{id}', [HoaDonController::class, 'status']);
});

Route::prefix('/nhan-vien')->group(function () {
    Route::post('list', [NhanVienController::class, 'list']);
    Route::post('create', [NhanVienController::class, 'create']);
    Route::get('list-name', [NhanVienController::class, 'listName']);
});

Route::prefix('/quyen-han')->group(function () {
    Route::get('list', [QuyenHanController::class, 'list']);
});

Route::prefix('/chuc-vu')->group(function () {
    Route::get('list', [ChucVuController::class, 'list']);
    Route::post('quyen-han', [ChucVuController::class, 'listQuyenHan']);
    Route::post('create', [ChucVuController::class, 'create']);
    Route::post('update', [ChucVuController::class, 'update']);
});

Route::prefix('/kho')->group(function () {
    Route::post('product', [KhoController::class, 'detailKho']);
});

Route::prefix('/luong')->group(function () {
    Route::post('select', [BangLuongController::class, 'selectLuong']);
});
