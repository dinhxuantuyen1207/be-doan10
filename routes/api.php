<?php

use App\Http\Controllers\BangLuongController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ChiTietGioHangController;
use App\Http\Controllers\ChucVuController;
use App\Http\Controllers\ChuDeFAQController;
use App\Http\Controllers\DanhGiaSanPhamController;
use App\Http\Controllers\DongSanPhamController;
use App\Http\Controllers\FAQController;
use App\Http\Controllers\GioHangController;
use App\Http\Controllers\HoaDonController;
use App\Http\Controllers\KhoController;
use App\Http\Controllers\LoaiSanPhamController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\NguoiDungController;
use App\Http\Controllers\NhanVienController;
use App\Http\Controllers\QLKhoController;
use App\Http\Controllers\QuyenHanController;
use App\Http\Controllers\SanPhamController;
use App\Http\Controllers\ThongBaoController;
use App\Http\Controllers\ThongBaoNhanVienController;
use App\Http\Controllers\TrangThaiController;
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
    Route::get('/name', [NguoiDungController::class, 'name']);
    Route::post('/forgot-password', [NguoiDungController::class, 'forgotPassword']);
    Route::post('/get-email', [NguoiDungController::class, 'getEmail']);
    Route::post('/check-code', [NguoiDungController::class, 'checkCode']);
    Route::post('/change-pass', [NguoiDungController::class, 'changePass']);
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
    Route::get('/all', [SanPhamController::class, 'getAll']);
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
    Route::post('/remove', [GioHangController::class, 'remove']);
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
    Route::post('/login', [NhanVienController::class, 'login']);
    Route::get('edit/{id}', [NhanVienController::class, 'edit']);
    Route::post('update', [NhanVienController::class, 'update']);
    Route::post('destroy', [NhanVienController::class, 'destroy']);
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
    Route::post('create', [KhoController::class, 'nhapKho']);
    Route::post('kho',[QLKhoController::class, 'kho']);
});

Route::prefix('/luong')->group(function () {
    Route::post('select', [BangLuongController::class, 'selectLuong']);
    Route::post('check-cham-cong', [BangLuongController::class, 'checkChamCong']);
    Route::post('cham-cong', [BangLuongController::class, 'chamCong']);
});

Route::prefix('/hoa-don')->group(function () {
    Route::post('create', [HoaDonController::class, 'create']);
    Route::post('add-info', [HoaDonController::class, 'addNguoiNhan']);
    Route::post('xac-thuc-hoa-don', [HoaDonController::class, 'xacThucHoaDon']);
    Route::post('xac-thuc', [HoaDonController::class, 'xacThuc']);
    Route::get('get/{id}', [HoaDonController::class, 'getHoaDon']);
    Route::post('list-hoa-don', [HoaDonController::class, 'listHoaDon']);
    Route::post('list-completed', [HoaDonController::class, 'listHoaDonHoanThanh']);
    Route::post('thong-ke', [HoaDonController::class, 'thongKe']);
    Route::post('update-status', [HoaDonController::class, 'updateStatus']);
    Route::post('update-pay', [HoaDonController::class, 'updatePay']);
});

Route::prefix('/trang-thai')->group(function () {
    Route::post('list-r', [TrangThaiController::class, 'listForRole']);
});

Route::prefix('/thong-bao')->group(function () {
    Route::post('user', [ThongBaoController::class, 'notificationUser']);
    Route::get('admin', [ThongBaoNhanVienController::class, 'notificationAdmin']);
});

Route::post('message', [ChatController::class, 'message']);
Route::prefix('/chat')->group(function () {
    Route::post('user', [ChatController::class, 'chatUser']);
    Route::get('admin', [ChatController::class, 'chatAdmin']);
});

Route::prefix('/danh-gia')->group(function () {
    Route::post('list-review', [DanhGiaSanPhamController::class, 'listReview']);
    Route::post('wait-review', [DanhGiaSanPhamController::class, 'waitReview']);
    Route::post('review', [DanhGiaSanPhamController::class, 'review']);
});

Route::prefix('/tin-tuc')->group(function () {
    Route::post('create', [NewsController::class, 'create']);
    Route::get('list', [NewsController::class, 'list']);
    Route::get('edit/{id}', [NewsController::class, 'edit']);
    Route::get('view/{id}', [NewsController::class, 'view']);
    Route::post('update', [NewsController::class, 'update']);
    Route::post('destroy', [NewsController::class, 'destroy']);
});
