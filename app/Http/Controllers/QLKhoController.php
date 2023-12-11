<?php

namespace App\Http\Controllers;

use App\Models\QLKho;
use Illuminate\Http\Request;

class QLKhoController extends Controller
{
    public function kho(Request $request)
    {
        $pre_page = 20;
        $search = '';
        if (isset($request->pre_page)) {
            $pre_page = $request->pre_page;
        }
        if (isset($request->search)) {
            $search = $request->search;
        }
        $qlKho = QLKho::with([
            "sanPham" => function ($query) use ($search) {
                $query->select('id', 'ten_san_pham')
                    ->where('ten_san_pham', 'like', '%' . $search . '%')
                    ->with(['hinhAnh' => function ($query) {
                        $query->select('id_san_pham', 'hinh_anh_san_pham');
                    }]);
            }
        ])
            ->whereHas('sanPham', function ($query) use ($search) {
                $query->where('ten_san_pham', 'like', '%' . $search . '%');
            })
            ->paginate($pre_page);

        if ($qlKho->isNotEmpty()) {
            return response()->json(['status' => true, 'data' => $qlKho]);
        } else {
            return response()->json(['status' => false]);
        }
    }
}
