<?php

namespace App\Http\Controllers;

use App\Models\QuyenHan;
use Illuminate\Http\Request;

class QuyenHanController extends Controller
{
    public function list(){
        $data = QuyenHan::get();
        return response()->json(['status'=> true , 'data'=>$data]);
    }
}
