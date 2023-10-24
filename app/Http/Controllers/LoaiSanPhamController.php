<?php

namespace App\Http\Controllers;

use App\Models\LoaiSanPham;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class LoaiSanPhamController extends Controller
{
    public function create(Request $request)
    {
        $kiem_tra_lsp = LoaiSanPham::where('ten_loai_san_pham', $request->ten_loai_san_pham)->where('id_dong_san_pham', $request->id_dong_san_pham)->first();
        if ($kiem_tra_lsp) {
            return response()->json(['mess' => 'Loại Sản Phẩm Đã Tồn Tại !']);
        } else {
            try {
                LoaiSanPham::create(['ten_loai_san_pham' => $request->ten_loai_san_pham, 'id_dong_san_pham' => $request->id_dong_san_pham]);
                $data = LoaiSanPham::select('id', 'ten_loai_san_pham', 'id_dong_san_pham')->get();
                return response()->json(['status' => true, 'data' => $data]);
            } catch (ModelNotFoundException $e) {
                return response()->json(['status' => false]);
            }
        }
    }

    public function list()
    {
        $data = LoaiSanPham::select('id', 'ten_loai_san_pham', 'id_dong_san_pham')->get();
        return response()->json(['data' => $data]);
    }

    public function listId(Request $request)
    {
        if (isset($request->id)) {
            $data = LoaiSanPham::where('id_dong_san_pham', $request->id)->select('id', 'ten_loai_san_pham')->get();
            return response()->json(['status' => true, 'data' => $data]);
        } else {
            return response()->json(['status' => false]);
        }
    }

    public function destroy(Request $request)
    {
        $kiem_tra_lsp = LoaiSanPham::where('id', $request->id)->first();
        if ($kiem_tra_lsp) {
            try {
                $kiem_tra_lsp->delete();
                $data = $this->reLoad();
                return response()->json(['status' => true, 'data' => $data]);
            } catch (ModelNotFoundException $e) {
                return response()->json(['status' => false]);
            }
        } else {
            return response()->json(['mess' => 'Dòng Sản Phẩm Không Tồn Tại !']);
        }
    }

    public function edit(Request $request)
    {
        $kiem_tra_lsp = LoaiSanPham::find($request->id);
        if ($kiem_tra_lsp) {
            try {
                $kiem_tra_lsp['ten_loai_san_pham'] = $request->ten_loai_san_pham;
                $kiem_tra_lsp['id_dong_san_pham'] = $request->id_dong_san_pham;
                $kiem_tra_lsp->save();
                $data = $this->reLoad();
                return response()->json(['status' => true, 'data' => $data]);
            } catch (ModelNotFoundException $e) {
                return response()->json(['status' => false]);
            }
        } else {
            return response()->json(['mess' => 'Dòng Sản Phẩm Không Tồn Tại !']);
        }
    }

    public function reLoad()
    {
        $data = LoaiSanPham::select('id', 'ten_loai_san_pham', 'id_dong_san_pham')->get();
        return $data;
    }
}
