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

    public function listName()
    {
        $data = NhanVien::select('id', 'ten_nhan_vien')->get();
        return response()->json(['status' => true, "data" => $data]);
    }

    public function login(Request $request)
    {
        $mat_khau = $request->mat_khau;
        $tai_khoan = $request->tai_khoan;
        $user = NhanVien::where('tai_khoan', $tai_khoan)->with('chucVu')->first();
        if ($user) {
            if (password_verify($mat_khau, $user->mat_khau)) {
                session(['id_admin' => $user->id]);
                return response()->json(['status' => true, 'id' => $user->id, 'name' => $user->ten_nhan_vien, 'avatar' => $user->anh_nhan_vien, 'role' => 'admin', 'chucvu' => $user->chucVu->list_quyen_han]);
            } else {
                return response()->json(['status' => false]);
            }
        } else {
            return response()->json(['status' => false]);
        }
    }

    public function edit($id)
    {
        try {
            if (!isset($id)) {
                return response()->json(['status' => false, 'message' => 'Info Error !']);
            }
            $data = NhanVien::find($id)->with('chucVu');
            if ($data) {
                return response()->json(['status' => true, 'data' => $data]);
            } else {
                return response()->json(['status' => false, 'message' => 'Not Found !']);
            }
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function update(Request $request)
    {
        try {
            $filename = '';
            $data = NhanVien::find($request->id);
            if (isset($data)) {
                if (isset($request->name) && $data->ten_nhan_vien != $request->name) {
                    $data->ten_nhan_vien = $request->name;
                }
                if (isset($request->password) && $data->mat_khau != $request->password) {
                    $data->mat_khau = bcrypt($request->password);
                }
                if (isset($request->phone) && $data->so_dien_thoai != $request->phone) {
                    $data->so_dien_thoai = $request->phone;
                }
                if (isset($request->salary) && $data->luong_co_ban != $request->salary) {
                    $data->luong_co_ban = $request->salary;
                }
                if (isset($request->id_position) && $data->id_chuc_vu != $request->id_position) {
                    $data->id_chuc_vu = $request->id_position;
                }
                if ($request->hasFile('avatar')) {
                    $files = $request->file('avatar');
                    if ($request->hasFile('avatar')) {
                        $filename = 'upload/' . $data->anh_nhan_vien;
                        $name = time() . rand(1, 100) . "." . $files->getClientOriginalExtension();
                        $data->anh_nhan_vien = $name;
                        $files->move('upload', $name);
                    }
                }
                $img_CCCD = [];
                if(isset($request->img_CCCD)){
                    $files2 = $request->img_CCCD;
                    if (isset($files2)) {
                        foreach ($files2 as $file) {
                            $name = time() . rand(1, 100) . "." . $file->getClientOriginalExtension();
                            $file->move('upload', $name);
                            $img_CCCD[] = $name;
                        }
                        if (isset($request->pre_img_CCCD)) {
                            foreach ($request->pre_img_CCCD as $img) {
                                $img_CCCD[] = $img;
                            }
                        }
                    }
                } else {
                    if (isset($request->pre_img_CCCD)) {
                        foreach ($request->pre_img_CCCD as $img) {
                            $img_CCCD[] = $img;
                        }
                    }
                }
                $data->anh_cccd = $img_CCCD;
                $data->save();
                if ($request->hasFile('image')) {
                    if (file_exists(public_path($filename))) {
                        unlink(public_path($filename));
                    }
                }
                return response()->json(['status' => true]);
            }
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function destroy(Request $request)
    {
        try {
            if (isset($request->id)) {
                $nhanvien = NhanVien::find($request->id);
                if (isset($nhanvien)) {
                    $nhanvien->delete();
                    return response()->json(['status' => true]);
                }
                return response()->json(['status' => false, 'message' => 'Not Found !']);
            }
            return response()->json(['status' => false, 'message' => 'Not Found !']);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()]);
        }
    }
}
