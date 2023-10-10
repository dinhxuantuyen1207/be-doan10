<?php

namespace App\Http\Controllers;

use App\Models\DongSanPham;
use App\Models\LoaiSanPham;
use GuzzleHttp\Psr7\Message;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class DongSanPhamController extends Controller
{
    public function create(Request $request){
        $kiem_tra_dsp = DongSanPham::where('ten_dong_san_pham', $request->ten_dong_san_pham)->first();
        if($kiem_tra_dsp){
            return response()->json(['mess'=>'Dòng Sản Phẩm Đã Tồn Tại !']);
        } else {
            try {
                DongSanPham::create(['ten_dong_san_pham' => $request->ten_dong_san_pham]);
                $data = DongSanPham::select('id','ten_dong_san_pham')->get();
                return response()->json(['status'=> true, 'data'=>$data]);
            } catch (ModelNotFoundException $e) {
                return response()->json(['status' => false]);
            }
        }
    }

    public function destroy(Request $request){
        $kiem_tra_dsp = DongSanPham::where('id', $request->id)->first();
        if($kiem_tra_dsp){
            try {
                $kiem_tra_dsp->delete();
                $kiem_tra_lsp = LoaiSanPham::where('id_dong_san_pham',$request->id)->get();
                foreach ($kiem_tra_lsp as $lsp) {
                    $lsp->delete();
                }
                $data = $this->reloadData();
                $data_child = $this->reLoadLSP();
                return response()->json(['status'=> true, 'data'=>$data,'data_child'=>$data_child]);
            } catch (ModelNotFoundException $e) {
                return response()->json(['status' => false]);
            }
        } else {
            return response()->json(['mess'=>'Dòng Sản Phẩm Không Tồn Tại !']);
        }
    }

    public function edit(Request $request){
        $kiem_tra_dsp = DongSanPham::find($request->id);
        if($kiem_tra_dsp){
            try {
                $kiem_tra_dsp['ten_dong_san_pham'] = $request->ten_dong_san_pham;
                $kiem_tra_dsp->save();
                $data = $this->reloadData();
                return response()->json(['status'=> true, 'data'=>$data]);
            } catch (ModelNotFoundException $e) {
                return response()->json(['status' => false]);
            }
        } else {
            return response()->json(['mess'=>'Dòng Sản Phẩm Không Tồn Tại !']);
        }
    }

    public function list(){
        $data = DongSanPham::select('id','ten_dong_san_pham')->get();
        return response()->json(['data' => $data]);
    }

    public function reloadData(){
        $data = DongSanPham::select('id','ten_dong_san_pham')->get();
        return $data;
    }

    public function reLoadLSP(){
        $dataLSP = LoaiSanPham::select('id','ten_loai_san_pham','id_dong_san_pham')->get();
        return $dataLSP;
    }
}
