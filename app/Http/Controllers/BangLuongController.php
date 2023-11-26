<?php

namespace App\Http\Controllers;

use App\Models\BangLuong;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    public function checkChamCong(Request $request)
    {
        try {
            if (!isset($request->id)) {
                return response()->json(["status" => false, "message" => "Login Please !"]);
            }

            if (!isset($request->month)) {
                return response()->json(["status" => false]);
            }
            $check = BangLuong::where('id_nhan_vien', $request->id)->where('thang_nam', $request->month)->select('cham_cong')->first();
            return response()->json(['status' => true, 'data' =>  $check]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'error' => $e->getMessage()]);
        }
    }

    public function chamCong(Request $request)
    {
        try {
            if (!isset($request->id)) {
                return response()->json(["status" => false, "message" => "Login Please !"]);
            }

            if (!isset($request->month)) {
                return response()->json(["status" => false]);
            }

            if (!isset($request->day)) {
                return response()->json(["status" => false]);
            }
            $chamCong = BangLuong::where('id_nhan_vien', $request->id)->where('thang_nam', $request->month)->first();
            if ($chamCong) {
                $cong = json_decode($chamCong->cham_cong, true);
                if (in_array($request->day, $cong)) {
                    return response()->json(['status' => false]);
                } else {
                    array_push($cong, $request->day * 1);
                    $chamCong->cham_cong = $cong;
                    $chamCong->save();
                    return response()->json(['status' => true]);
                }
            } else {
                $new_arr = [$request->day * 1];

                $new = BangLuong::create(['id_nhan_vien' => $request->id, 'thang_nam' => $request->month, 'he_so' => 1]);
                if ($new) {
                    $new->cham_cong = $new_arr;
                    $new->save();
                    return response()->json(['status' => true]);
                } else {
                    return response()->json(['status' => false]);
                }
            }
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'error' => $e->getMessage()]);
        }
    }
}
