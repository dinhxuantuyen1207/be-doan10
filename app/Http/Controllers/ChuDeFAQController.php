<?php

namespace App\Http\Controllers;

use App\Models\ChuDeFAQ;
use App\Models\FAQ;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChuDeFAQController extends Controller
{
    public function list()
    {
        try {
            $data = ChuDeFAQ::select('id', 'ten_chu_de')->with(['fAQ' => function ($query) {
                $query->select('id_chu_de', 'cau_hoi', 'cau_tra_loi');
            }])->get();
            return response()->json(["status" => true, "data" => $data]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function listTitle()
    {
        try {
            $data = ChuDeFAQ::select('id', 'ten_chu_de')->get();
            return response()->json(["status" => true, "data" => $data]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function create(Request $request)
    {
        try {
            if (isset($request->ten_chu_de)) {
                $ten_chu_de = $request->ten_chu_de;
                $data_pre = ChuDeFAQ::where('ten_chu_de', $ten_chu_de)->count();
                if ($data_pre > 0) {
                    return response()->json(["status" => false, 'message' => "Tên Chủ Để FAQ Đã Tồn Tại"]);
                } else {
                    $data = ChuDeFAQ::create(['ten_chu_de' => $ten_chu_de]);
                    return response()->json(["status" => true]);
                }
            } else {
                return response()->json(["status" => false]);
            }
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function destroy(Request $request)
    {
        try {
            if (!isset($request->id)) {
                return response()->json(["status" => false, "message" => "Missing 'id' in request"]);
            }

            DB::beginTransaction();

            $data_pre = ChuDeFAQ::find($request->id);

            if (!$data_pre) {
                DB::rollBack();
                return response()->json(["status" => false, "message" => "Record not found"]);
            }

            $data_child = FAQ::where('id_chu_de', $request->id)->get();

            if ($data_child->count() > 0) {
                foreach ($data_child as $child) {
                    $child->delete();
                }
            }

            $data_pre->delete();
            DB::commit();
            return response()->json(["status" => true]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'error' => $e->getMessage()]);
        }
    }
}
