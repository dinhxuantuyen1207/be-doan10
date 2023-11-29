<?php

namespace App\Http\Controllers;

use App\Models\ChiTietHoaDon;
use App\Models\DanhGiaSanPham;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DanhGiaSanPhamController extends Controller
{
    public function listReview(Request $request)
    {
        try {
            if (!isset($request->id)) {
                return response()->json(["status" => false, "message" => "Please Login!"]);
            }
            $arr_data = [];
            $data = ChiTietHoaDon::with([
                'danhGia' => function ($query) {
                    $query->orderBy('created_at', 'asc');
                },
                'hoaDon' => function ($query) use ($request) {
                    $query->where('id_nguoi_dung', $request->id)
                        ->select('id', 'id_nguoi_dung', 'trang_thai_thanh_toan', 'id_trang_thai');
                },
                'sanPham' => function ($query) {
                    $query->select('id', 'ten_san_pham')->has('hinhAnh')->with([
                        'hinhAnh' => function ($query) {
                            $query->select('id', 'id_san_pham', 'hinh_anh_san_pham');
                        },
                    ]);
                },
            ])
                ->where('id_danh_gia', '!=', null)
                ->select('id', 'id_san_pham', 'id_hoa_don', 'id_danh_gia')
                ->get();
            $data = $data->filter(function ($chiTiet) use (&$arr_data) {
                if ($chiTiet->hoaDon != null) {
                    array_push($arr_data, $chiTiet);
                }
            });
            return response()->json(['status' => true, 'data' => $arr_data]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'error' => $e->getMessage()]);
        }
    }

    public function waitReview(Request $request)
    {
        try {
            if (!isset($request->id)) {
                return response()->json(["status" => false, "message" => "Please Login!"]);
            }
            $arr_data = [];
            $data = ChiTietHoaDon::with([
                'danhGia',
                'hoaDon' => function ($query) use ($request) {
                    $query->where('id_nguoi_dung', $request->id)
                        ->select('id', 'id_nguoi_dung', 'trang_thai_thanh_toan', 'id_trang_thai')->where('trang_thai_thanh_toan', 'Đã Thanh Toán')->where('id_trang_thai', 7);
                },
                'sanPham' => function ($query) {
                    $query->select('id', 'ten_san_pham')->has('hinhAnh')->with([
                        'hinhAnh' => function ($query) {
                            $query->select('id', 'id_san_pham', 'hinh_anh_san_pham');
                        },
                    ]);
                },
            ])
                ->where('id_danh_gia', '=', null)
                ->select('id', 'id_san_pham', 'id_hoa_don', 'id_danh_gia')
                ->get();
            $data = $data->filter(function ($chiTiet) use (&$arr_data) {
                if ($chiTiet->hoaDon != null) {
                    array_push($arr_data, $chiTiet);
                }
            });
            return response()->json(['status' => true, 'data' => $arr_data]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'error' => $e->getMessage()]);
        }
    }

    public function review(Request $request)
    {
        try {
            if (!isset($request->id)) {
                return response()->json(["status" => false, "message" => "Please Login!"]);
            }
            if (!isset($request->id_invoice) || !isset($request->comment) || !isset($request->id_invoice) || !isset($request->star) || !isset($request->id_product)) {
                return response()->json(["status" => false, "message" => "Review Error!"]);
            }
            DB::beginTransaction();
            $review = DanhGiaSanPham::create([
                'id_san_pham' => $request->id_product,
                'sao_danh_gia' => $request->star,
                'binh_luan_danh_gia' => $request->comment,
                'id_nguoi_dung' => $request->id
            ]);
            if ($review) {
                $chiTietHoaDon = ChiTietHoaDon::where('id_hoa_don', $request->id_invoice)->where('id_san_pham', $request->id_product)->first();
                if ($chiTietHoaDon) {
                    $chiTietHoaDon->id_danh_gia = $review->id;
                    $chiTietHoaDon->save();
                    DB::commit();
                    return response()->json(['status' => true]);
                }
            }
            DB::rollback();
            return response()->json(['status' => false]);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json(['status' => false, 'error' => $e->getMessage()]);
        }
    }
}
