<?php

namespace App\Http\Controllers;

use App\Models\DanhGiaSanPham;
use App\Models\HinhAnhSanPham;
use App\Models\LoaiSanPham;
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
    public function edit($id){
        try {
            $data = SanPham::find($id);
            if(isset($data)){
                $img = HinhAnhSanPham::where('id_san_pham',$id)->get();
                return response()->json(['status'=> true , 'data'=>$data ,'img'=>$img]);
            } else {
                return response()->json(['status'=> false]);
            }
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function list(){
        try{
            $data = [];
            $data_pre = SanPham::select('id', 'ten_san_pham', 'gia', 'khuyen_mai','id_loai_san_pham')
                ->get();

                foreach ($data_pre as $sanPham) {
                    $loaiSanPham = LoaiSanPham::find($sanPham->id_loai_san_pham);
                    $sanPham->ten_loai_san_pham = $loaiSanPham->ten_loai_san_pham;
                    $data[]= $sanPham;
                }
            $length = sizeof($data);
            return response()->json(['status'=> true , 'data' => $data , 'length' => $length]);
        }catch (Exception $e){
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function detail(Request $request){
        try {
            $id = $request->id;
            $sanPham = SanPham::find($id);
            if(isset($sanPham)){
                $hinhAnh = HinhAnhSanPham::where('id_san_pham',$id)->select('hinh_anh_san_pham')->get();
                $danhGia = DanhGiaSanPham::where('id_san_pham',$id)->get();
                return response()->json(['status' => true,'data'=> $sanPham,'image'=>$hinhAnh,'reviews'=>$danhGia]);
            } else {
                return response()->json(['status' => false]);
            }
        }catch (Exception $e){
            return response()->json(['error' => $e->getMessage()]);
        }
    }
}
