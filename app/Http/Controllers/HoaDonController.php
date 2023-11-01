<?php

namespace App\Http\Controllers;

use App\Models\ChiTietGioHang;
use App\Models\ChiTietHoaDon;
use App\Models\GioHang;
use App\Models\HoaDon;
use App\Models\SanPham;
use App\Models\ThongTinNguoiNhan;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HoaDonController extends Controller
{
    public function hoaDonListUser($id)
    {
        try {
            $data = HoaDon::where('id_nguoi_dung', $id)->select('id', 'gia_tien_thanh_toan')->get();
            return response()->json(['status' => true, 'data' => $data]);
        } catch (Exception $e) {
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
            $data_pre = HoaDon::with(['nguoiDung' => function ($query1) {
                $query1->select('id', 'tai_khoan');
            }, 'trangThai' => function ($query) {
                $query->select('id', 'trang_thai');
            }])
                ->where(function ($query) use ($search) {
                    $query->where('trang_thai_thanh_toan', 'like', '%' . $search . '%')
                        ->orWhereHas('trangThai', function ($subquery) use ($search) {
                            $subquery->where('trang_thai', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('nguoiDung', function ($subquery) use ($search) {
                            $subquery->where('tai_khoan', 'like', '%' . $search . '%');
                        });
                })
                ->select('id', 'ngay_mua', 'gia_tien_thanh_toan', 'trang_thai_thanh_toan', 'id_nguoi_dung', 'id_trang_thai')
                ->paginate($pre_page);
            return response()->json(['status' => true, 'data' => $data_pre]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function status($id)
    {
        try {
            $data_pre = HoaDon::with(['nguoiDung' => function ($query) {
                $query->select('id', 'tai_khoan', 'so_dien_thoai', 'ten_nguoi_dung', 'email');
            }, 'trangThai' => function ($query) {
                $query->select('id', 'trang_thai');
            }, 'trangThaiHoaDon' => function ($query) {
                $query->select('id', 'id_hoa_don', 'id_nhan_vien', 'id_trang_thai', 'ngay_cap_nhap', 'ghi_chu')->orderBy('id', 'asc')->with(["trangThai" => function ($query) {
                    $query->select('id', 'trang_thai');
                }, 'nhanVien' => function ($query) {
                    $query->select('id', 'ten_nhan_vien');
                }]);
            }])
                ->find($id);
            return response()->json(['status' => true, 'data' => $data_pre]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function create(Request $request)
    {
        try {
            if (!isset($request->id)) {
                return response()->json(["status" => false, "message" => "Yêu Cầu Đăng Nhập"]);
            }
            if (!isset($request->amount)) {
                return response()->json(["status" => false, "message" => "Không Có Sản Phẩm Để Thanh Toán"]);
            }
            if (!isset($request->tax)) {
                return response()->json(["status" => false, "message" => "Thiếu Thông Tin"]);
            }
            if (!isset($request->ship)) {
                return response()->json(["status" => false, "message" => "Thiếu Thông Tin"]);
            }
            if (!isset($request->name) || !isset($request->phone) || !isset($request->address)) {
                return response()->json(["status" => false, "message" => "Yêu Cầu Nhập Thông Tin Người Nhận"]);
            }


            DB::beginTransaction();

            $gioHang = GioHang::where('id_nguoi_dung', $request->id)->first();

            if (!$gioHang) {
                DB::rollBack();
                return response()->json(["status" => false, "message" => "Shopping cart not found"]);
            }

            $chiTietGioHang = ChiTietGioHang::where('id_gio_hang', $gioHang->id)->get();

            if ($chiTietGioHang->isEmpty()) {
                DB::rollBack();
                return response()->json(["status" => false, "message" => "Shopping cart items not found"]);
            }

            $today = Carbon::today();
            $date = $today->format('Y-m-d');

            $hoaDon = HoaDon::create([
                'id_nguoi_dung' => $request->id,
                'ngay_mua' => $date,
                'gia_tien_thanh_toan' => $request->amount,
                'id_trang_thai' => 1,
                'tax' => $request->tax,
                'ship' => $request->ship,
                'trang_thai_thanh_toan' => 'Chưa Thanh Toán'
            ]);

            if (isset($request->status) && $request->status == true) {
                $hoaDon->trang_thai_thanh_toan = "Đã Thanh Toán";
                $hoaDon->ngay_thanh_toan = $date;
                $hoaDon->save();
            }

            foreach ($chiTietGioHang as $chiTiet) {
                $sanPham = SanPham::find($chiTiet->id_san_pham);
                if ($sanPham) {
                    $newChiTiet = ChiTietHoaDon::create([
                        'id_hoa_don' => $hoaDon->id,
                        'id_san_pham' => $chiTiet->id_san_pham,
                        'gia_tien' => $sanPham->gia,
                        'so_luong' => $chiTiet->so_luong,
                    ]);

                    if ($newChiTiet) {
                        $chiTiet->delete();
                    }
                }
            }
            ThongTinNguoiNhan::create(['id_hoa_don' => $hoaDon->id, 'ten_nguoi_nhan' => $request->name, 'so_dien_thoai' => $request->phone, 'dia_chi' => $request->address]);
            $gioHang->delete();

            DB::commit();

            return response()->json(["status" => true, 'invoiceID' => $hoaDon->id]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'error' => $e->getMessage()]);
        }
    }

    public function xacThuc(Request $request)
    {
        try {
            if (!isset($request->id)) {
                return response()->json(["status" => false, "message" => "Yêu Cầu Đăng Nhập"]);
            }

            DB::beginTransaction();

            $hoaDon = HoaDon::find($request->id);

            if (isset($request->status) && $request->status == true) {
                $today = Carbon::today();
                $date = $today->format('Y-m-d');
                $hoaDon->trang_thai_thanh_toan = "Đã Thanh Toán";
                $hoaDon->ngay_thanh_toan = $date;
                $hoaDon->save();

                DB::commit();

                return response()->json(['status' => true, 'message' => 'Đã cập nhật trạng thái thanh toán']);
            }
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'error' => $e->getMessage()]);
        }
    }

    public function getHoaDon($id)
    {
        if (isset($id)) {
            $hoaDon = HoaDon::with(["ThongTinNguoiNhan", "chiTietHoaDon" => function ($query) {
                $query->with(['sanPham' => function ($query1) {
                    $query1->select('id', 'ten_san_pham');
                }]);
            }])->find($id);
            return response()->json(['status' => true, 'data' => $hoaDon]);
        }
        return response()->json(['status' => false]);
    }
}
