<?php

namespace App\Http\Controllers;

use App\Models\ChuDeFAQ;
use App\Models\FAQ;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FAQController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function list()
    {
        try {
            $data = ChuDeFAQ::with(['fAQ' => function ($query) {
                $query->select('cau_hoi', 'cau_tra_loi');
            }])->get();
            return response()->json(["status" => true, "data" => $data]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function listFAQ(Request $request)
    {
        if (isset($request->id)) {
            $id = $request->id;
            $data_pre = FAQ::where('id_chu_de', $id)->get();
            if (isset($data_pre)) {
                return response()->json(["status" => true, 'data' => $data_pre]);
            } else {
                return response()->json(["status" => false]);
            }
        } else {
            return response()->json(["status" => false]);
        }
    }

    public function create(Request $request)
    {
        if (isset($request->id_chu_de) && isset($request->cau_hoi) && isset($request->cau_tra_loi)) {
            $data_pre = FAQ::create(['id_chu_de' => $request->id_chu_de, 'cau_hoi' => $request->cau_hoi, 'cau_tra_loi' => $request->cau_tra_loi]);
            if (isset($data_pre)) {
                return response()->json(["status" => true]);
            } else {
                return response()->json(["status" => false]);
            }
        } else {
            return response()->json(["status" => false]);
        }
    }

    public function update(Request $request)
    {
        try {
            if (!isset($request->id)) {
                return response()->json(["status" => false, "message" => "Missing 'id' in request"]);
            }
            $data = FAQ::find($request->id);

            if (!$data) {
                return response()->json(["status" => false, "message" => "Record not found"]);
            }
            $data->cau_hoi = $request->cau_hoi;
            $data->cau_tra_loi = $request->cau_tra_loi;
            $data->save();
            return response()->json(["status" => true, "message" => "Record updated successfully"]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'error' => $e->getMessage()]);
        }
    }

    public function destroy(Request $request)
    {
        try {
            if (!isset($request->id)) {
                return response()->json(["status" => false, "message" => "Missing 'id' in request"]);
            }
            DB::beginTransaction();
            $data_pre = FAQ::find($request->id);

            if (!$data_pre) {
                DB::rollBack();
                return response()->json(["status" => false, "message" => "Record not found"]);
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
