<?php

namespace App\Http\Controllers;

use App\Models\ChuDeFAQ;
use App\Models\FAQ;
use Exception;
use Illuminate\Http\Request;

class FAQController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function list()
    {
        try{
        $data = ChuDeFAQ::with(['fAQ' => function ($query) {
            $query->select('cau_hoi','cau_tra_loi');
        }])->get();
            return response()->json(["status"=>true,"data"=>$data]);
        }catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

}
