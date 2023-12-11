<?php

namespace App\Http\Controllers;

use App\Models\Kho;
use App\Models\QLKho;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KhoController extends Controller
{
    public function detailKho(Request $request)
    {
        if (isset($request->id)) {
            $data = Kho::with(['nhanVien' => function ($query) {
                $query->select('id', 'ten_nhan_vien');
            }])->where('id_san_pham', $request->id)->orderByRaw('YEAR(ngay_nhap) DESC, MONTH(ngay_nhap) DESC')
                ->get();

            $groupedData = [];

            foreach ($data as $item) {
                $thang = date('m', strtotime($item->ngay_nhap));
                $nam = date('Y', strtotime($item->ngay_nhap));
                $itemData = ['item' => $item];

                if (!isset($groupedData[$nam])) {
                    $groupedData[$nam] = [];
                }

                if (!isset($groupedData[$nam][$thang])) {
                    $groupedData[$nam][$thang] = [$itemData];
                } else {
                    $groupedData[$nam][$thang][] = $itemData;
                }
            }
            return response()->json(["status" => true, "data" => $groupedData]);
        } else {
            return response()->json(['status' => false]);
        }
    }

    public function nhapKho(Request $request)
    {
        try {
            if (!isset($request->id)) {
                return response()->json(["status" => false, "message" => "Login please !"]);
            }
            if (!isset($request->id_item)) {
                return response()->json(["status" => false, "message" => "Item not found !"]);
            }
            if (!isset($request->quantity)) {
                return response()->json(["status" => false, "message" => "Input quantity !"]);
            }
            if (!isset($request->gia)) {
                return response()->json(["status" => false, "message" => "Input price !"]);
            }
            $today = Carbon::today();
            $date = $today->format('Y-m-d');
            $nhapKho = Kho::create(['id_san_pham' => $request->id_item, 'id_nhan_vien' => $request->id, 'so_luong_nhap' => $request->quantity, 'gia_nhap' => $request->gia, 'ngay_nhap' => $date]);
            if ($nhapKho) {
                $kho = QLKho::where('id_san_pham', $request->id_item)->first();
                if (!isset($kho)) {
                    $khoC = QLKho::create(['id_san_pham' => $request->id_item, 'so_luong_nhap' => $request->quantity, 'so_luong_da_ban' => 0]);
                } else {
                    $kho->so_luong_nhap += $request->quantity;
                    $kho->save();
                };
                return response()->json(['status' => true, 'message' => 'Create Successfully']);
            } else {
                return response()->json(['status' => false]);
            }
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'error' => $e->getMessage()]);
        }
    }
}
