<?php

namespace App\Http\Controllers;

use App\Models\NhanVien;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NhanVienController extends Controller
{
    public function list(Request $request)
    {
        try {
            $pre_page = 20;
            $search = '';
            if (isset($request->pre_page)) {
                $pre_page = $request->pre_page;
            }
            if (isset($request->search)) {
                $search = $request->search;
            }
            $data_pre = NhanVien::with(['chucVu' => function ($query) {
                $query->select('id', 'ten_chuc_vu');
            }])
                ->where(function ($query) use ($search) {
                    $query->where('ten_nhan_vien', 'like', '%' . $search . '%')
                        ->orWhere('so_dien_thoai', 'like', '%' . $search . '%');
                })
                ->select('id', 'tai_khoan', 'ten_nhan_vien', 'so_dien_thoai', 'id_chuc_vu', 'luong_co_ban', 'anh_nhan_vien', 'anh_cccd')
                ->paginate($pre_page);
            $data_pre->each(function ($item) {
                $item->anh_cccd = json_decode($item->anh_cccd, true);
            });
            return response()->json(['status' => true, 'data' => $data_pre]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function create(Request $request)
    {
        try {
            return DB::transaction(function () use ($request) {
                $check_name = NhanVien::where('tai_khoan', $request->account)->first();
                if (isset($check_name)) {
                    return response()->json(['status' => false, 'message' => 'Người Dùng Đã Tồn Tại']);
                }
                $nhanVien = NhanVien::create([
                    'ten_nhan_vien' => $request->name,
                    'tai_khoan' => $request->account,
                    'mat_khau' => bcrypt($request->password),
                    'so_dien_thoai' => $request->phone,
                    'id_chuc_vu' => $request->id_position,
                    'luong_co_ban' => $request->salary
                ]);

                $avatar = $request->avatar;
                if (isset($avatar)) {
                    $name = time() . rand(1, 100) . "." . $avatar->getClientOriginalExtension();
                    $avatar->move('upload', $name);
                    $nhanVien->anh_nhan_vien = $name;
                }
                $img_CCCD = [];
                $files = $request->img_CCCD;
                if (isset($files)) {
                    foreach ($files as $file) {
                        $name = time() . rand(1, 100) . "." . $file->getClientOriginalExtension();
                        $file->move('upload', $name);
                        $img_CCCD[] = $name;
                    }
                }
                $nhanVien->anh_cccd = $img_CCCD;
                $nhanVien->save();

                return response()->json(['status' => true]);
            }, 5);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()]);
        }
    }
}
