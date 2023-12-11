<?php

namespace App\Http\Controllers;

use App\Events\NotificationEvent;
use App\Events\NotificationEventAdmin;
use App\Models\ChiTietGioHang;
use App\Models\ChiTietHoaDon;
use App\Models\GioHang;
use App\Models\HoaDon;
use App\Models\QLKho;
use App\Models\SanPham;
use App\Models\ThongBao;
use App\Models\ThongBaoNhanVien;
use App\Models\ThongTinNguoiNhan;
use App\Models\TrangThai;
use App\Models\TrangThaiHoaDon;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use \Illuminate\Support\Facades\Log;

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
            $role = [];
            if (isset($request->pre_page)) {
                $pre_page = $request->pre_page;
            }
            if (isset($request->search)) {
                $search = $request->search;
            }
            if (isset($request->role)) {
                if (in_array(13, $request->input('role', []))) {
                    $role = array_merge($role, [1, 11]);
                }
                if (in_array(14, $request->input('role', []))) {
                    $role = array_merge($role, [2, 3]);
                }
                if (in_array(15, $request->input('role', []))) {
                    $role = array_merge($role, [4, 5]);
                }
                if (in_array(16, $request->input('role', []))) {
                    $role = array_merge($role, [6]);
                }
                if (in_array(17, $request->input('role', []))) {
                    $role = array_merge($role, [8, 9, 10, 11, 12]);
                }
                if (in_array(18, $request->input('role', []))) {
                    $role = array_merge($role, [7]);
                }
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
                ->where('id_trang_thai', '!=', 99)
                ->whereIn('id_trang_thai', $role)
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
                return response()->json(["status" => false, "message" => "Login Please"]);
            }
            if (!$request->filled('data')) {
                return response()->json(["status" => false, "message" => "None Product Selected"]);
            }
            DB::beginTransaction();

            $amount = 0;
            $tax = 0;
            $ship = 10;
            foreach ($request->data as $item) {
                $sanPham = SanPham::find($item['id_san_pham']);
                $amount = $amount + ($item['so_luong'] * ($sanPham->gia - $sanPham->gia * ($sanPham->khuyen_mai / 100)));
            };
            $tax = $amount * 0.1;
            $tax = round($tax, 2);
            $amount = $amount + $tax + $ship;
            $amount = round($amount, 2);

            $today = Carbon::today();
            $date = $today->format('Y-m-d');

            $hoaDon = HoaDon::create([
                'id_nguoi_dung' => $request->id,
                'ngay_mua' => $date,
                'gia_tien_thanh_toan' => $amount,
                'id_trang_thai' => 99,
                'tax' => $tax,
                'ship' => $ship,
                'trang_thai_thanh_toan' => 'Chưa Thanh Toán'
            ]);

            foreach ($request->data as $item) {
                if ($item) {
                    $sanPham = SanPham::find($item['id_san_pham']);
                    $newChiTiet = ChiTietHoaDon::create([
                        'id_hoa_don' => $hoaDon->id,
                        'id_san_pham' => $item['id_san_pham'],
                        'gia_tien' => $sanPham->gia - $sanPham->gia * $sanPham->khuyen_mai / 100,
                        'so_luong' => $item['so_luong'],
                    ]);
                }
            }

            DB::commit();

            return response()->json(["status" => true, 'invoiceID' => $hoaDon->id, 'amount' => $amount]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'error' => $e->getMessage()]);
        }
    }

    public function addNguoiNhan(Request $request)
    {
        try {
            if (!isset($request->id)) {
                return response()->json(["status" => false, "message" => "No Find Invoice Number"]);
            }
            if (!isset($request->name) || !isset($request->phone) || !isset($request->address)) {
                return response()->json(["status" => false, "message" => "Input Information Please"]);
            }

            DB::beginTransaction();

            $info = ThongTinNguoiNhan::create(['id_hoa_don' => $request->id, 'ten_nguoi_nhan' => $request->name, 'so_dien_thoai' => $request->phone, 'dia_chi' => $request->address]);

            DB::commit();

            if (isset($info)) {
                return response()->json(["status" => true]);
            } else {
                return response()->json(["status" => false]);
            }
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'error' => $e->getMessage()]);
        }
    }

    public function xacThucHoaDon(Request $request)
    {
        try {
            if (!isset($request->id_user)) {
                return response()->json(["status" => false, "message" => "Please Login!"]);
            }
            if (!isset($request->id)) {
                return response()->json(["status" => false, "message" => "Not found Number Invoice"]);
            }
            $hoaDon = HoaDon::find($request->id);
            if (isset($hoaDon)) {
                $hoaDon->id_trang_thai = 1;
                $hoaDon->trang_thai_thanh_toan = $request->thanh_toan;
                $hoaDon->save();
                $today = Carbon::today();
                $date = $today->format('Y-m-d');
                $txt_thong_bao = 'Order ' . $hoaDon->id . ' is waiting for confirmation';
                $notification1 = ThongBaoNhanVien::create([
                    'id_nhan_vien' => 1,
                    'thong_bao' => $txt_thong_bao,
                    'ngay_thong_bao' => $date,
                    'trang_thai_thong_bao' => 0
                ]);
                if (isset($notification1)) {
                    broadcast(new NotificationEventAdmin($notification1->id_nhan_vien, $notification1));
                }
                if ($request->thanh_toan == "Chờ Xác Nhận Thanh Toán") {
                    $txt_thong_bao = 'Order ' . $hoaDon->id . ' is waiting for pay confirmation';
                    $notification = ThongBaoNhanVien::create([
                        'id_nhan_vien' => 1,
                        'thong_bao' => $txt_thong_bao,
                        'ngay_thong_bao' => $date,
                        'trang_thai_thong_bao' => 0
                    ]);
                    if (isset($notification)) {
                        broadcast(new NotificationEventAdmin($notification->id_nhan_vien, $notification));
                    }
                }
                $gioHang = GioHang::where('id_nguoi_dung', $request->id_user)->first();
                if (isset($gioHang)) {
                    ChiTietGioHang::where('id_gio_hang', $gioHang->id)->delete();
                }
                return response()->json(['status' => true, 'message' => 'Update Successfully']);
            } else {
                return response()->json(['status' => true, 'message' => 'Not found Number Invoice']);
            }
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'error' => $e->getMessage()]);
        }
    }

    public function xacThuc(Request $request)
    {
        try {
            if (!isset($request->id_user)) {
                return response()->json(["status" => false, "message" => "Please Login!"]);
            }
            if (!isset($request->id)) {
                return response()->json(["status" => false, "message" => "Login Please"]);
            }

            DB::beginTransaction();

            $hoaDon = HoaDon::find($request->id);

            if (isset($request->status) && $request->status == true) {
                $today = Carbon::today();
                $date = $today->format('Y-m-d');
                $hoaDon->trang_thai_thanh_toan = "Đã Thanh Toán";
                $hoaDon->ngay_thanh_toan = $date;
                $hoaDon->id_trang_thai = 1;
                $hoaDon->save();
                $txt_thong_bao = 'Order ' . $hoaDon->id . ' is waiting for confirmation';
                $notification = ThongBaoNhanVien::create([
                    'id_nhan_vien' => 1,
                    'thong_bao' => $txt_thong_bao,
                    'ngay_thong_bao' => $date,
                    'trang_thai_thong_bao' => 0
                ]);
                broadcast(new NotificationEventAdmin($notification->id_nhan_vien, $notification));
                $gioHang = GioHang::where('id_nguoi_dung', $request->id_user)->first();
                if (isset($gioHang)) {
                    ChiTietGioHang::where('id_gio_hang', $gioHang->id)->delete();
                }
                DB::commit();

                return response()->json(['status' => true, 'message' => 'Update Successfully']);
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

    public function listHoaDon(Request $request)
    {
        if (!isset($request->id)) {
            return response()->json(["status" => false, "message" => "Login Please"]);
        } else {
            $hoaDon = HoaDon::with(["chiTietHoaDon" => function ($query) {
                $query->with(["sanPham" => function ($query) {
                    $query->with(["hinhAnh"])->select('id', 'ten_san_pham');
                }]);
            }])->where('id_nguoi_dung', $request->id)->where('id_trang_thai', '!=', 99)->orderBy("ngay_mua", "desc")->get();
            return response()->json(['status' => true, 'data' => $hoaDon]);
        }
    }

    public function thongKe(Request $request)
    {
        try {
            $main_data = [];
            $days = [];
            $tax = 0;
            $ship = 0;
            $total = 0;
            $data = HoaDon::with(["ChiTietHoaDon" => function ($query) use ($request) {
                $query->with(["sanPham" => function ($query) {
                    $query->select('id', 'ten_san_pham');
                }])->select('id', 'id_hoa_don', 'id_san_pham', 'so_luong', 'gia_tien');
                if ($request->id_product != null) {
                    $query->where('id_san_pham', $request->id_product);
                };
            }])->select('id', 'ngay_mua', 'tax', 'ship', 'gia_tien_thanh_toan')->orderBy('ngay_mua', 'asc')->where('ngay_mua', '<=', $request->day_to)
                ->where('ngay_mua', '>=', $request->day_from)->get();

            $productCounts = [];
            foreach ($data as $order) {
                $tax = $tax + $order['tax'];
                $ship = $ship + $order['ship'];
                $total = $total + $order['gia_tien_thanh_toan'];
                $ngayMua  = $order['ngay_mua'];
                $chiTietHoaDon = $order['ChiTietHoaDon'];
                if ($chiTietHoaDon != null) {
                    foreach ($chiTietHoaDon as $item) {
                        $sanPham = $item['SanPham'];
                        $sanPhamName = $sanPham->ten_san_pham;
                        $soLuong = $item['so_luong'];
                        $giaTien = $item['so_luong'] * $item['gia_tien'];
                        if (array_key_exists($ngayMua, $productCounts)) {
                            if (array_key_exists($sanPhamName, $productCounts[$ngayMua])) {
                                $productCounts[$ngayMua][$sanPhamName]['soLuong'] += $soLuong;
                                $productCounts[$ngayMua][$sanPhamName]['giaTien'] += $giaTien;
                            } else {
                                $productCounts[$ngayMua][$sanPhamName] = [
                                    'soLuong' => $soLuong,
                                    'giaTien' => $giaTien,
                                ];
                            }
                        } else {
                            $productCounts[$ngayMua] = [
                                $sanPhamName => [
                                    'soLuong' => $soLuong,
                                    'giaTien' => $giaTien,
                                ],
                            ];
                        }
                    }
                }
            }

            foreach ($productCounts as $ngay => $productCount) {
                if (!in_array($ngay, $days)) {
                    $days[] = $ngay;
                }
                foreach ($productCount as $sanPhamId => ['soLuong' => $soLuong, 'giaTien' => $giaTien]) {
                    $giaTien = round($giaTien, 2);
                    $entry = [
                        'Date' => $ngay,
                        'Item' => $sanPhamId,
                        'Count' => $soLuong,
                        'Price' => $giaTien
                    ];
                    $main_data[] = $entry;
                }
            }
            $price = [
                'tax' => round($tax, 2),
                'ship' => round($ship, 2),
                'total' => round($total, 2)
            ];
            return response()->json(['status' => true, 'data' => $main_data, 'days' => $days, 'price' => $price]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    public function listHoaDonHoanThanh(Request $request)
    {
        if (!isset($request->id)) {
            return response()->json(["status" => false, "message" => "Login Please"]);
        } else {
            $hoaDon = HoaDon::with(["chiTietHoaDon" => function ($query) {
                $query->with(["sanPham" => function ($query) {
                    $query->with(["hinhAnh"])->select('id', 'ten_san_pham');
                }]);
            }])->where('id_nguoi_dung', $request->id)->where('id_trang_thai', '=', 7)->orderBy("ngay_mua", "desc")->get();
            return response()->json(['status' => true, 'data' => $hoaDon]);
        }
    }

    public function updateStatus(Request $request)
    {
        try {
            if (!isset($request->id)) {
                return response()->json(["status" => false, "message" => "Invoice Not Found"]);
            }
            if (!isset($request->id_trang_thai)) {
                return response()->json(["status" => false, "message" => "Status Not Found"]);
            }
            DB::beginTransaction();
            $data = HoaDon::find($request->id);
            $today = Carbon::today();
            $date = $today->format('Y-m-d');
            if ($data) {
                $data->id_trang_thai = $request->id_trang_thai;
                $trang_thai = TrangThai::find($request->id_trang_thai);
                $txt_thong_bao = 'Order ' . $data->id . ' is in status ' . $trang_thai->trang_thai;
                $notification = ThongBao::create([
                    'id_nguoi_dung' => $data->id_nguoi_dung,
                    'thong_bao' => $txt_thong_bao,
                    'ngay_thong_bao' => $date,
                    'trang_thai_thong_bao' => 0
                ]);
                broadcast(new NotificationEvent($notification->id_nguoi_dung, $notification));
                $data->save();
            }
            $trang_thai = TrangThaiHoaDon::create([
                'id_hoa_don' => $data->id,
                'id_trang_thai' => $request->id_trang_thai,
                'id_nhan_vien' => $request->id_nhan_vien,
                'ngay_cap_nhap' => $date,
                'ghi_chu' => $request->ghi_chu ?? ''
            ]);
            if ($request->id_trang_thai == 2) {
                $chiTiet = ChiTietHoaDon::where('id_hoa_don', $data->id)->get();
                foreach ($chiTiet as $ct) {
                    $kho = QLKho::where('id_san_pham', $ct->id)->first();
                    if (isset($kho)) {
                        $kho->so_luong_da_ban = $kho->so_luong_da_ban * 1 + $ct->so_luong * 1;
                        $kho->save();
                    }
                }
            }
            DB::commit();
            return response()->json(["status" => true]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'error' => $e->getMessage()]);
        }
    }

    public function updatePay(Request $request)
    {
        try {
            if (!isset($request->id)) {
                return response()->json(["status" => false, "message" => "Invoice Not Found"]);
            }
            if (!isset($request->id_trang_thai_thanh_toan)) {
                return response()->json(["status" => false, "message" => "Status Not Found"]);
            }
            if (isset($request->role)) {
                if (!in_array(19, $request->input('role', []))) {
                    return response()->json(["status" => false, "message" => "Not Have Access"]);
                } else {
                    DB::beginTransaction();
                    $data = HoaDon::find($request->id);
                    if ($data) {
                        $today = Carbon::today();
                        $date = $today->format('Y-m-d');
                        if ($request->id_trang_thai_thanh_toan == 1) {
                            $data->trang_thai_thanh_toan = 'Chưa Thanh Toán';
                        } else if ($request->id_trang_thai_thanh_toan == 2) {
                            $data->trang_thai_thanh_toan = 'Chờ Xác Nhận Thanh Toán';
                        } else if ($request->id_trang_thai_thanh_toan == 3) {
                            $data->ngay_thanh_toan = $date;
                            $data->trang_thai_thanh_toan = 'Đã Thanh Toán';
                            $today = Carbon::today();
                            $date2 = $today->format('Y-m-d');
                            $txt_thong_bao = 'Order ' . $data->id . ' has been determined to be paid';
                            $notification = ThongBao::create([
                                'id_nguoi_dung' => $data->id_nguoi_dung,
                                'thong_bao' => $txt_thong_bao,
                                'ngay_thong_bao' => $date2,
                                'trang_thai_thong_bao' => 0
                            ]);
                            if (isset($notification)) {
                                broadcast(new NotificationEvent($notification->id_nguoi_dung, $notification));
                            }
                        }
                        $data->save();
                    }
                    DB::commit();
                    return response()->json(["status" => true]);
                }
            }
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'error' => $e->getMessage()]);
        }
    }
}
