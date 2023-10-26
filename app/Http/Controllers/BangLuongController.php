<?php

namespace App\Http\Controllers;

use App\Models\BangLuong;
use Exception;
use Illuminate\Http\Request;

class BangLuongController extends Controller
{
    public function selectLuong(Request $request)
    {
        try {
            $id_nv = '';
            $month = '';
            $data_pre = BangLuong::with(['nhanVien' => function ($query1) {
                $query1->select('id', 'ten_nhan_vien', 'luong_co_ban');
            }])
                ->select('id', 'id_nhan_vien', 'thang_nam', 'cham_cong', 'he_so', 'thuong');
            if (isset($request->id)) {
                $id_nv = $request->id;
                $data_pre->where('id_nhan_vien', $id_nv);
            }
            if (isset($request->month)) {
                $month = $request->month;
                $data_pre->where('thang_nam', $month);
            }

            $data = $data_pre->get();

            return response()->json(['status' => true, 'data' => $data]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }
}
