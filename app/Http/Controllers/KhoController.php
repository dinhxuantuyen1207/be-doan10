<?php

namespace App\Http\Controllers;

use App\Models\Kho;
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
}
