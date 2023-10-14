<?php

namespace App\Http\Controllers;

use App\Models\NguoiDung;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NguoiDungController extends Controller
{
    public function list()
    {
        $data = NguoiDung::get();
        return response()->json(['data' => $data]);
    }

    public function profile(Request $request)
    {
        $id = $request->id;
        $data = NguoiDung::find($id);
        return response()->json(['data' => $data]);
    }

    public function update(Request $request,)
    {
        $taikhoan = NguoiDung::find($request->id);
        if (isset($request->ten_nguoi_dung) && $request->ten_nguoi_dung != '') {
            $taikhoan->ten_nguoi_dung = $request->ten_nguoi_dung;
        }
        if (isset($request->dia_chi) && $request->dia_chi != '') {
            $taikhoan->dia_chi = $request->dia_chi;
        }
        if (isset($request->so_dien_thoai) && $request->so_dien_thoai != '') {
            $taikhoan->so_dien_thoai = $request->so_dien_thoai;
        }
        $taikhoan->save();
        return response()->json(['status' => true]);
    }

    public function changePassword(Request $request)
    {
        $taikhoan = NguoiDung::find($request->id);
        $taikhoan->mat_khau = $request->mat_khau;
        $taikhoan['mat_khau'] = bcrypt($taikhoan['mat_khau']);
        $taikhoan->save();
        return response()->json(['status' => true]);
    }

    public function destroy(Request $request)
    {
        $taikhoan = NguoiDung::find($request->id);
        if ($taikhoan) {
            $taikhoan->delete();
            return response()->json(['status' => true]);
        }
        return response()->json(['status' => false]);
    }

    public function create(Request $request)
    {
        try{
            $check_name = NguoiDung::where('tai_khoan',$request->tai_khoan)->first();
            if(isset($check_name)){
                return response()->json(['status' => false,'message' => 'Người Dùng Đã Tồn Tại']);
            }
            $check_phone = NguoiDung::where('so_dien_thoai',$request->so_dien_thoai)->first();
            if(isset($check_phone)){
                return response()->json(['status' => false,'message' => 'Số Điện Thoại Đã Được Sử Dụng']);
            }
            $data = $request->all();
            $data['mat_khau'] = bcrypt($data['mat_khau']);
            NguoiDung::create($data);
            return response()->json(['status' => true]);
        }catch (Exception $e) {
            return response()->json(['status' => false]);
        }
    }

    public function login(Request $request)
    {

        $mat_khau = $request->mat_khau;
        $tai_khoan = $request->tai_khoan;
        $user = NguoiDung::where('tai_khoan',$tai_khoan)->first();
        if ($user){
            if(password_verify($mat_khau,$user->mat_khau)){
                return response()->json(['status' => true,'id'=>$user->id,'name'=>$user->ten_nguoi_dung]);
            } else {
                return response()->json(['status' => false]);
            }
        }else {
            return response()->json(['status' => false]);
        }
    }
}
