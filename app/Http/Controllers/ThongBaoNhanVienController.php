<?php

namespace App\Http\Controllers;

use App\Models\ThongBaoNhanVien;
use Exception;
use Illuminate\Http\Request;

class ThongBaoNhanVienController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function notificationAdmin()
    {
        try {
            $thong_bao = ThongBaoNhanVien::orderBy('created_at', 'desc')->get();
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
    public function show(ThongBaoNhanVien $thongBaoNhanVien)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ThongBaoNhanVien $thongBaoNhanVien)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ThongBaoNhanVien $thongBaoNhanVien)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ThongBaoNhanVien $thongBaoNhanVien)
    {
        //
    }
}
