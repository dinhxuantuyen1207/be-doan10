<?php

namespace App\Http\Controllers;

use App\Models\ChiTietGioHang;
use App\Models\GioHang;
use App\Models\SanPham;
use Exception;
use Illuminate\Http\Request;

class GioHangController extends Controller
{
    public function addToCart(Request $request)
    {
        try {
            $id = $request->id;
            if (!isset($id)) {
                return response()->json(['status' => false, 'message' => 'Missing user ID']);
            }

            $gio_hang = GioHang::where('id_nguoi_dung', $id)->get()->count();
            $gio_hang_get = GioHang::where('id_nguoi_dung', $id)->first();
            if ($gio_hang > 0) {
                $check_gio_hang = ChiTietGioHang::where('id_gio_hang',$gio_hang_get->id)->where('id_san_pham',$request->id_san_pham)->get()->count();
                if($check_gio_hang > 0){
                    return response()->json(['status' => true, 'message' => 'Sản Phẩm Đã Có Trong Giỏ Hàng']);
                } else {
                    ChiTietGioHang::create(['id_gio_hang' => $gio_hang_get->id, 'id_san_pham' => $request->id_san_pham, 'so_luong' => 1]);
                    return response()->json(['status' => true, 'message' => 'Đã Thêm Vào Giỏ Hàng']);
                }
            }
            $new_gio_hang = GioHang::create(['id_nguoi_dung' => $id]);
            if (!$new_gio_hang) {
                return response()->json(['status' => false, 'message' => 'Failed to create cart']);
            }
            ChiTietGioHang::create(['id_gio_hang' => $new_gio_hang->id, 'id_san_pham' => $request->id_san_pham, 'so_luong' => 1]);

            return response()->json(['status' => true, 'message' => 'Đã Thêm Vào Giỏ Hàng']);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'error' => $e->getMessage()]);
        }
    }

    public function myCart(Request $request)
    {
        try {
            $id = $request->id;
            $data = GioHang::where('id_nguoi_dung',$id)->select('id')->with(['chiTietGioHang' => function ($query) { $query->with(["sanPham" => function ($query) {
                $query->select('id','gia','khuyen_mai','ten_san_pham')->with(['hinhAnh' => function ($query) { $query->select('id_san_pham','hinh_anh_san_pham')->first();}]);
            } ])->select('id','id_gio_hang','id_san_pham','so_luong'); }])->first();
            return response()->json(['status' => true, 'data' => $data]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'error' => $e->getMessage()]);
        }
    }

}
