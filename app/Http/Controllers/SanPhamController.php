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
            if(isset($files)){
                foreach ($files as $file) {
                    $name = time() . rand(1, 100) . "." . $file->getClientOriginalExtension();
                    $file->move('upload', $name);
                    HinhAnhSanPham::create([
                        'id_san_pham' => $sanPham->id,
                        "hinh_anh_san_pham" => $name
                    ]);
                }
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
                $category = LoaiSanPham::where('id',$data->id_loai_san_pham)->first();
                $img = HinhAnhSanPham::where('id_san_pham',$id)->get();
                return response()->json(['status'=> true , 'data'=>$data ,'img'=>$img,'category'=>$category]);
            } else {
                return response()->json(['status'=> false]);
            }
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function list(){
        try{
            $data = [];;
            $so_luong = SanPham::count();
            $data_pre = SanPham::with('loaiSanPham')->with('hinhAnh')->select('id', 'ten_san_pham', 'gia', 'khuyen_mai', 'id_loai_san_pham')->paginate(12);
            // $data_pre = SanPham::select('id', 'ten_san_pham', 'gia', 'khuyen_mai','id_loai_san_pham')
            //     ->paginate(12);
            //     foreach ($data_pre as $sanPham) {
            //         $loaiSanPham = LoaiSanPham::find($sanPham->id_loai_san_pham);
            //         $sanPham->ten_loai_san_pham = $loaiSanPham->ten_loai_san_pham;
            //         $data[]= $sanPham;
            //     }
            // $length = sizeof($data);
            return response()->json(['status'=> true , 'data' => $data_pre , 'length' => $so_luong]);
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

    public function update(Request $request){
        try {
            return DB::transaction(function () use ($request) {
                $id = $request->id_product;
                $files = $request->image;
                $sanPham = SanPham::find($id);
                if(isset($sanPham)){
                    $sanPham->ten_san_pham      = $request->name;
                    $sanPham->gia               = $request->price;
                    $sanPham->id_loai_san_pham  = $request->id_category;
                    $sanPham->mo_ta_ngan        = $request->short_description;
                    $sanPham->mo_ta             = $request->description;
                    $sanPham->khuyen_mai        = $request->sale;
                    $sanPham->save();
                    if(isset($request->del_img)){
                        foreach($request->del_img as $id_del_img){
                            $hinhAnh = HinhAnhSanPham::find($id_del_img);
                            $filename = 'upload/' . $hinhAnh->hinh_anh_san_pham;
                            if(file_exists(public_path($filename))){
                                unlink(public_path($filename));
                            }
                            $hinhAnh->delete();
                        }
                        $hinhAnh = HinhAnhSanPham::whereIn('id', $request->del_img)->delete();
                    }
                    if(isset($files)){
                        foreach ($files as $file) {
                            $name = time() . rand(1, 100) . "." . $file->getClientOriginalExtension();
                            $file->move('upload', $name);
                            HinhAnhSanPham::create([
                                'id_san_pham' => $sanPham->id,
                                "hinh_anh_san_pham" => $name
                            ]);
                        }
                    }
                    return response()->json(['status' => true]);
                } else {
                    return response()->json(['status' => false]);
                }
            }, 5);
        }catch (Exception $e){
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()]);
        }
    }
}
