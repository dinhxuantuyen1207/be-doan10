<?php

namespace App\Http\Controllers;

use App\Models\DongSanPham;
use GuzzleHttp\Psr7\Message;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class DongSanPhamController extends Controller
{
    public function create(Request $request){
        $kiem_tra_dsp = DongSanPham::where('ten_dong_san_pham', $request->dong_san_pham)->first();
        if($kiem_tra_dsp){
            return response()->json(['mess'=>'Dòng Sản Phẩm Đã Tồn Tại !']);
        } else {
            try {
                DongSanPham::create(['ten_dong_san_pham' => $request->dong_san_pham]);
                return response()->json(['status'=>true]);
            } catch (ModelNotFoundException $e) {
                return response()->json(['status' => false]);
            }
        }
    }

    public function destroy(Request $request){
        $kiem_tra_dsp = DongSanPham::where('id', $request->id_dong_san_pham)->first();
        if($kiem_tra_dsp){
            try {
                $kiem_tra_dsp->delete();
                return response()->json(['status'=>true]);
            } catch (ModelNotFoundException $e) {
                return response()->json(['status' => false]);
            }
        } else {
            return response()->json(['mess'=>'Dòng Sản Phẩm Không Tồn Tại !']);
        }
    }

    public function edit(Request $request){
        $kiem_tra_dsp = DongSanPham::find($request->id_dong_san_pham);
        if($kiem_tra_dsp){
            try {
                $kiem_tra_dsp['ten_dong_san_pham'] = $request->ten_dong_san_pham;
                $kiem_tra_dsp->save();
                return response()->json(['status'=>true]);
            } catch (ModelNotFoundException $e) {
                return response()->json(['status' => false]);
            }
        } else {
            return response()->json(['mess'=>'Dòng Sản Phẩm Không Tồn Tại !']);
        }
    }
}
