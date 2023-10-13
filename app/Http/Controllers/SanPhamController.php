<?php

namespace App\Http\Controllers;

use App\Models\HinhAnhSanPham;
use App\Models\SanPham;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SanPhamController extends Controller
{

    public function create(Request $request)
{
    try {
        return DB::transaction(function () use ($request) {
            $sanPham = SanPham::create([
                'ten_san_pham' => $request->name,
                'gia' => $request->price,
                'id_loai_san_pham' => $request->id_category,
                'mo_ta_ngan' => $request->short_description,
                'mo_ta' => $request->description,
                'khuyen_mai' => $request->sale,
            ]);

            $files = $request->image;

            foreach ($files as $file) {
                $name = time() . rand(1, 100) . "." . $file->getClientOriginalExtension();
                $file->move('upload', $name);
                HinhAnhSanPham::create([
                    'id_san_pham' => $sanPham->id,
                    "hinh_anh_san_pham" => $name
                ]);
            }

            return response()->json(['status' => true]);
        }, 5);
    } catch (Exception $e) {
        DB::rollBack();
        return response()->json(['error' => $e->getMessage()]);
    }
}
}
