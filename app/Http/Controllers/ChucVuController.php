<?php

namespace App\Http\Controllers;

use App\Models\ChucVu;
use Exception;
use Illuminate\Http\Request;


class ChucVuController extends Controller
{
    public function list()
    {
        $data = ChucVu::select("id", 'ten_chuc_vu')->get();
        return response()->json(["status" => true, "data" => $data]);
    }

    public function listQuyenHan(Request $request)
    {
        if (isset($request->id)) {
            $id = $request->id;
            $data_pre = ChucVu::select('id', 'list_quyen_han')->find($id);

            if (isset($data_pre)) {
                $data = json_decode($data_pre->list_quyen_han, true);
                return response()->json(["status" => true, 'data' => $data]);
            } else {
                return response()->json(["status" => false]);
            }
        } else {
            return response()->json(["status" => false]);
        }
    }

    public function create(Request $request)
    {
        try {
            if (isset($request->ten_chuc_vu)) {
                $ten_chuc_vu = $request->ten_chuc_vu;
                $data_pre = ChucVu::where('ten_chuc_vu', $ten_chuc_vu)->count();
                if ($data_pre > 0) {
                    return response()->json(["status" => false, 'message' => "Tên Chức Vụ Đã Tồn Tại"]);
                } else {
                    $data = ChucVu::create(['ten_chuc_vu' => $ten_chuc_vu]);
                    return response()->json(["status" => true, 'data' => $data]);
                }
            } else {
                return response()->json(["status" => false]);
            }
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function update(Request $request)
    {
        try {
            if (isset($request->id)) {
                $id = $request->id;
                $data_pre = ChucVu::find($id);
                if ($request->list_quyen_han == null) {
                    $data_pre->list_quyen_han = [];
                } else {
                    $data_pre->list_quyen_han = $request->list_quyen_han;
                }
                $data_pre->save();
                return response()->json(["status" => true]);
            } else {
                return response()->json(["status" => false]);
            }
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }
}
