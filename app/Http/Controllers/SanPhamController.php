<?php

namespace App\Http\Controllers;

use App\Models\DanhGiaSanPham;
use App\Models\HinhAnhSanPham;
use App\Models\LoaiSanPham;
use App\Models\SanPham;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SanPhamController extends Controller
{

    public function create(Request $request)
    {
        try {
            return DB::transaction(function () use ($request) {
                $sanPham = SanPham::create([
                    'ten_san_pham' => $request->name,
                    'gia' => $request->price,
                    'id_loai_san_pham' => $request->id_category,
                    'mo_ta_ngan' => $request->short_description,
                    'mo_ta' => $request->description,
                    'khuyen_mai' => $request->sale,
                ]);

                $files = $request->image;
                if (isset($files)) {
                    foreach ($files as $file) {
                        $name = time() . rand(1, 100) . "." . $file->getClientOriginalExtension();
                        $file->move('upload', $name);
                        HinhAnhSanPham::create([
                            'id_san_pham' => $sanPham->id,
                            "hinh_anh_san_pham" => $name
                        ]);
                    }
                }

                return response()->json(['status' => true]);
            }, 5);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()]);
        }
    }
    public function edit($id)
    {
        try {
            $data = SanPham::find($id);
            if (isset($data)) {
                $category = LoaiSanPham::where('id', $data->id_loai_san_pham)->first();
                $img = HinhAnhSanPham::where('id_san_pham', $id)->get();
                return response()->json(['status' => true, 'data' => $data, 'img' => $img, 'category' => $category]);
            } else {
                return response()->json(['status' => false]);
            }
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }



    // public function list()
    // {
    //     try {
    //         $data_pre = SanPham::with('loaiSanPham')->with('hinhAnh')->select('id', 'ten_san_pham', 'gia', 'khuyen_mai', 'id_loai_san_pham')->paginate(12);

    //         return response()->json(['status' => true, 'data' => $data_pre]);
    //     } catch (Exception $e) {
    //         return response()->json(['error' => $e->getMessage()]);
    //     }
    // }

    public function listId(Request $request)
    {
        if (isset($request->id)) {
            $data = SanPham::where('id_loai_san_pham', $request->id)->select('id', 'ten_san_pham')->get();
            return response()->json(['status' => true, 'data' => $data]);
        } else {
            return response()->json(['status' => false]);
        }
    }

    public function listAll(Request $request)
    {
        try {
            $pre_page = 20;
            $search = '';
            if (isset($request->pre_page)) {
                $pre_page = $request->pre_page;
            }
            if (isset($request->search)) {
                $search = $request->search;
            }
            $data_pre = SanPham::with(['loaiSanPham' => function ($query1) {
                $query1->select('id', 'id_dong_san_pham', 'ten_loai_san_pham');
            }, 'hinhAnh' => function ($query) {
                $query->select('id', 'id_san_pham', 'hinh_anh_san_pham');
            }])
                ->where(function ($query) use ($search) {
                    $query->where('ten_san_pham', 'like', '%' . $search . '%')
                        ->orWhereHas('loaiSanPham', function ($subquery) use ($search) {
                            $subquery->where('ten_loai_san_pham', 'like', '%' . $search . '%');
                        });
                })
                ->select('id', 'ten_san_pham', 'gia', 'khuyen_mai', 'id_loai_san_pham', 'mo_ta_ngan', 'mo_ta')
                ->paginate($pre_page);
            return response()->json(['status' => true, 'data' => $data_pre]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function detail(Request $request)
    {
        try {
            $id = $request->id;
            $sanPham = SanPham::find($id);
            if (isset($sanPham)) {
                $hinhAnh = HinhAnhSanPham::where('id_san_pham', $id)->select('hinh_anh_san_pham')->get();
                $danhGia = DanhGiaSanPham::where('id_san_pham', $id)->get();
                return response()->json(['status' => true, 'data' => $sanPham, 'image' => $hinhAnh, 'reviews' => $danhGia]);
            } else {
                return response()->json(['status' => false]);
            }
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function productDetail($id)
    {
        try {
            $sanPham = SanPham::with(['loaiSanPham' => function ($query1) {
                $query1->select('id', 'ten_loai_san_pham');
            }])->find($id);
            $saoDanhGia = 0;
            $soLuot = 0;
            $saoTong = 0;
            if (isset($sanPham)) {
                $hinhAnh = HinhAnhSanPham::where('id_san_pham', $id)->select('hinh_anh_san_pham')->get();
                $danhGia = DanhGiaSanPham::where('id_san_pham', $id)->select('id', 'id_san_pham', 'sao_danh_gia', 'binh_luan_danh_gia', 'id_nguoi_dung')->with(['nguoiDanhGia' => function ($query1) {
                    $query1->select('id', 'ten_nguoi_dung', 'so_dien_thoai');
                }])->get();
                if ($danhGia->count() > 0) {
                    foreach ($danhGia as $item) {
                        $saoDanhGia = $saoDanhGia + $item->sao_danh_gia;
                        $soLuot = $soLuot + 1;
                    }
                    $saoTong = $saoDanhGia / $soLuot;
                }
                $sanPhamPhu = SanPham::where('id_loai_san_pham', $sanPham->id_loai_san_pham)->where('id', '!=', $sanPham->id)->with(['hinhAnh' => function ($query) {
                    $query->select('id', 'id_san_pham', 'hinh_anh_san_pham');
                }, 'loaiSanPham' => function ($query) {
                    $query->select('id', 'ten_loai_san_pham');
                }])->select('id', 'ten_san_pham', 'gia', 'id_loai_san_pham', 'khuyen_mai')->take(8)->get();
                return response()->json(['status' => true, 'data' => $sanPham, 'image' => $hinhAnh, 'reviews' => $danhGia, 'starRating' => $saoTong, 'sanPhamPhu' => $sanPhamPhu]);
            } else {
                return response()->json(['status' => false]);
            }
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function update(Request $request)
    {
        try {
            return DB::transaction(function () use ($request) {
                $id = $request->id_product;
                $files = $request->image;
                $sanPham = SanPham::find($id);
                if (isset($sanPham)) {
                    $sanPham->ten_san_pham      = $request->name;
                    $sanPham->gia               = $request->price;
                    $sanPham->id_loai_san_pham  = $request->id_category;
                    $sanPham->mo_ta_ngan        = $request->short_description;
                    $sanPham->mo_ta             = $request->description;
                    $sanPham->khuyen_mai        = $request->sale;
                    $sanPham->save();
                    if (isset($request->del_img)) {
                        foreach ($request->del_img as $id_del_img) {
                            $hinhAnh = HinhAnhSanPham::find($id_del_img);
                            $filename = 'upload/' . $hinhAnh->hinh_anh_san_pham;
                            if (file_exists(public_path($filename))) {
                                unlink(public_path($filename));
                            }
                            $hinhAnh->delete();
                        }
                        $hinhAnh = HinhAnhSanPham::whereIn('id', $request->del_img)->delete();
                    }
                    if (isset($files)) {
                        foreach ($files as $file) {
                            $name = time() . rand(1, 100) . "." . $file->getClientOriginalExtension();
                            $file->move('upload', $name);
                            HinhAnhSanPham::create([
                                'id_san_pham' => $sanPham->id,
                                "hinh_anh_san_pham" => $name
                            ]);
                        }
                    }
                    return response()->json(['status' => true]);
                } else {
                    return response()->json(['status' => false]);
                }
            }, 5);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function destroy(Request $request)
    {
        try {
            return DB::transaction(function () use ($request) {
                $id = $request->id;
                if (isset($id)) {
                    SanPham::find($id)->delete();
                    $hinhAnh = HinhAnhSanPham::where('id_san_pham', $id)->get();
                    foreach ($hinhAnh as $anh) {
                        $filename = 'upload/' . $anh->hinh_anh_san_pham;
                        if (file_exists(public_path($filename))) {
                            unlink(public_path($filename));
                        }
                        $anh->delete();
                    }
                    return response()->json(['status' => true]);
                } else {
                    return response()->json(['status' => false]);
                }
            }, 5);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function list(Request $request)
    {
        try {
            $listIdLoaiSanPham = [];
            $giaTien = [];
            $price = '';
            if (isset($request->id_category)) {
                $loaiSanPham = LoaiSanPham::where('id_dong_san_pham', $request->id_category)->select('id')->get();
                foreach ($loaiSanPham as $item) {
                    $listIdLoaiSanPham[] = $item->id;
                }
            }
            if (isset($request->gia_tien)) {
                $giaTien = $request->gia_tien;
            }
            if (isset($request->price)) {
                $price = $request->price;
            }
            $data_pre = SanPham::with('loaiSanPham', 'hinhAnh');

            if (isset($request->id_category)) {
                $data_pre->whereIn('id_loai_san_pham', $listIdLoaiSanPham);
            }
            if (!empty($giaTien[0])) {
                $data_pre->whereRaw('gia - gia*khuyen_mai/100 >= ?', [$giaTien[0]]);
            }

            if (!empty($giaTien[1]) && $giaTien != 0) {
                $data_pre->whereRaw('gia - gia*khuyen_mai/100 <= ?', [$giaTien[1]]);
            }

            $data_pre->select('id', 'ten_san_pham', 'gia', 'khuyen_mai', 'id_loai_san_pham');

            if (!empty($price)) {
                $data_pre->orderByRaw('gia - gia*khuyen_mai/100 ' . $price);
            }

            $data_pre = $data_pre->paginate(12);

            return response()->json(['status' => true, 'data' => $data_pre]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function search(Request $request)
    {
        try {
            $giaTien = [];
            $search = '';
            if (isset($request->gia_tien)) {
                $giaTien = $request->gia_tien;
            }
            if (isset($request->search)) {
                $search = $request->search;
            }

            $data_pre = SanPham::with(['loaiSanPham' => function ($query1) {
                $query1->select('id', 'id_dong_san_pham', 'ten_loai_san_pham')->with(['dongSanPham' => function ($query) {
                    $query->select('id', 'ten_dong_san_pham');
                }]);
            }, 'hinhAnh' => function ($query) {
                $query->select('id', 'id_san_pham', 'hinh_anh_san_pham');
            }])
                ->where(function ($query) use ($search) {
                    $query->where('ten_san_pham', 'like', '%' . $search . '%')
                        ->orWhereHas('loaiSanPham', function ($subquery) use ($search) {
                            $subquery->where('ten_loai_san_pham', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('loaiSanPham.dongSanPham', function ($subquery) use ($search) {
                            $subquery->where('ten_dong_san_pham', 'like', '%' . $search . '%');
                        });
                });
            if (!empty($giaTien[0])) {
                $data_pre->whereRaw('gia - gia*khuyen_mai/100 >= ?', [$giaTien[0]]);
            }

            if (!empty($giaTien[1]) && $giaTien != 0) {
                $data_pre->whereRaw('gia - gia*khuyen_mai/100 <= ?', [$giaTien[1]]);
            }

            $data_pre->select('id', 'ten_san_pham', 'gia', 'khuyen_mai', 'id_loai_san_pham');



            $data_pre = $data_pre->paginate(12);

            return response()->json(['status' => true, 'data' => $data_pre]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function getAll()
    {
        $data = SanPham::select('id', 'ten_san_pham')->get();
        if (count($data) > 0) {
            return response()->json(['status' => true, 'data' => $data]);
        } else {
            return response()->json(['status' => false]);
        }
    }
}
