<?php

namespace App\Http\Controllers;

use App\Models\ChiTietGioHang;
use Exception;
use Illuminate\Http\Request;

class ChiTietGioHangController extends Controller
{
    public function changeCart(Request $request)
    {
        try {
            $id = $request->id;
            $so_luong = $request->so_luong;
            $data = ChiTietGioHang::find($id);
            if (isset($data)) {
                $data->so_luong = $so_luong;
                $data->save();
            }
            return response()->json(['status' => true]);
        } catch (Exception $e) {
            return response()->json(['status' => false]);
        }
    }
}
