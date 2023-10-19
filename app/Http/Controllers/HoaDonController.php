<?php

namespace App\Http\Controllers;

use App\Models\HoaDon;
use Exception;
use Illuminate\Http\Request;

class HoaDonController extends Controller
{
    public function hoaDonListUser($id)
    {
        try {
            $data = HoaDon::where('id_nguoi_dung', $id)->select('id', 'gia_tien_thanh_toan')->get();
            return response()->json(['status' => true, 'data' => $data]);
        } catch (Exception $e) {
            return response()->json(['status' => false]);
        }
    }

    public function listAll(Request $request)
    {
        try {
            $pre_page = 20;
            $search = '';
            if (isset($request->pre_page)) {
                $pre_page = $request->pre_page;
            }
            if (isset($request->search)) {
                $search = $request->search;
            }
            $data_pre = HoaDon::with(['nguoiDung' => function ($query1) {
                $query1->select('id', 'tai_khoan');
            }, 'trangThai' => function ($query) {
                $query->select('id', 'trang_thai');
            }])
                ->where(function ($query) use ($search) {
                    $query->where('trang_thai_thanh_toan', 'like', '%' . $search . '%')
                        ->orWhereHas('trangThai', function ($subquery) use ($search) {
                            $subquery->where('trang_thai', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('nguoiDung', function ($subquery) use ($search) {
                            $subquery->where('tai_khoan', 'like', '%' . $search . '%');
                        });
                })
                ->select('id', 'ngay_mua', 'gia_tien_thanh_toan', 'trang_thai_thanh_toan','id_nguoi_dung','id_trang_thai')
                ->paginate($pre_page);
            return response()->json(['status' => true, 'data' => $data_pre]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function status($id)
    {
        try {
            $data_pre = HoaDon::with(['nguoiDung' => function ($query) {
                $query->select('id', 'tai_khoan','so_dien_thoai','ten_nguoi_dung','email');
            }, 'trangThai' => function ($query) {
                $query->select('id', 'trang_thai');
            },'trangThaiHoaDon' => function ($query) {
                $query->select('id', 'id_hoa_don','id_nhan_vien','id_trang_thai','ngay_cap_nhap','ghi_chu')->orderBy('id','asc')->with(["trangThai" => function($query) {
                    $query->select('id','trang_thai');
                },'nhanVien'=> function($query) {
                    $query->select('id','ten_nhan_vien');
                }]);
            }])
                ->find($id);
            return response()->json(['status' => true, 'data' => $data_pre]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }
}
