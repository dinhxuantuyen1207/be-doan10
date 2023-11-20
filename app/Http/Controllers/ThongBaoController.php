<?php

namespace App\Http\Controllers;

use App\Models\ThongBao;
use Exception;
use Illuminate\Http\Request;

class ThongBaoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function notificationUser(Request $request)
    {
        try {
            if (!isset($request->id)) {
                return response()->json(["status" => false, "message" => "User Not Found"]);
            }
            $thong_bao = ThongBao::where('id_nguoi_dung', $request->id)->orderBy('created_at', 'desc')->get();
            return response()->json(["status" => true, 'data' => $thong_bao]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(ThongBao $thongBao)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ThongBao $thongBao)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ThongBao $thongBao)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ThongBao $thongBao)
    {
        //
    }
}
