<?php

namespace App\Http\Controllers;

use App\Models\TrangThai;
use Exception;
use Illuminate\Http\Request;

class TrangThaiController extends Controller
{

    public function listForRole(Request $request)
    {
        try {
            $role = [];
            if (isset($request->role)) {
                if (in_array(13, $request->input('role', []))) {
                    $role = array_merge($role, [2, 12]);
                }
                if (in_array(14, $request->input('role', []))) {
                    $role = array_merge($role, [3, 4]);
                }
                if (in_array(15, $request->input('role', []))) {
                    $role = array_merge($role, [4, 5, 6, 9]);
                }
                if (in_array(16, $request->input('role', []))) {
                    $role = array_merge($role, [6, 7]);
                }
                if (in_array(17, $request->input('role', []))) {
                    $role = array_merge($role, [8, 9, 10, 11, 12]);
                }
            }
            $data_pre = TrangThai::whereIn('id', $role)->get();
            return response()->json(['status' => true, 'data' => $data_pre]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
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
    public function show(TrangThai $trangThai)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TrangThai $trangThai)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TrangThai $trangThai)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TrangThai $trangThai)
    {
        //
    }
}
