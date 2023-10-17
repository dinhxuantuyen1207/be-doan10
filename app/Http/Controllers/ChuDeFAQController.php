<?php

namespace App\Http\Controllers;

use App\Models\ChuDeFAQ;
use Exception;
use Illuminate\Http\Request;

class ChuDeFAQController extends Controller
{
    public function list()
    {
        try{
        $data = ChuDeFAQ::select('id','ten_chu_de')->with(['fAQ' => function ($query) {
            $query->select('id_chu_de','cau_hoi','cau_tra_loi');
        }])->get();
            return response()->json(["status"=>true,"data"=>$data]);
        }catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }
}
