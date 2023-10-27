<?php

namespace App\Http\Controllers;

use App\Models\ChiTietGioHang;
use App\Models\GioHang;
use App\Models\SanPham;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class GioHangController extends Controller
{
    public function addToCart(Request $request)
    {
        try {
            $id = $request->id;
            if (!isset($id)) {
                return response()->json(['status' => false, 'message' => 'Missing user ID']);
            }

            $gio_hang = GioHang::where('id_nguoi_dung', $id)->get()->count();
            $gio_hang_get = GioHang::where('id_nguoi_dung', $id)->first();
            if ($gio_hang > 0) {
                $check_gio_hang = ChiTietGioHang::where('id_gio_hang', $gio_hang_get->id)->where('id_san_pham', $request->id_san_pham)->get()->count();
                if ($check_gio_hang > 0) {
                    return response()->json(['status' => true, 'message' => 'Sản Phẩm Đã Có Trong Giỏ Hàng']);
                } else {
                    ChiTietGioHang::create(['id_gio_hang' => $gio_hang_get->id, 'id_san_pham' => $request->id_san_pham, 'so_luong' => 1]);
                    return response()->json(['status' => true, 'message' => 'Đã Thêm Vào Giỏ Hàng']);
                }
            }
            $new_gio_hang = GioHang::create(['id_nguoi_dung' => $id]);
            if (!$new_gio_hang) {
                return response()->json(['status' => false, 'message' => 'Failed to create cart']);
            }
            ChiTietGioHang::create(['id_gio_hang' => $new_gio_hang->id, 'id_san_pham' => $request->id_san_pham, 'so_luong' => 1]);

            return response()->json(['status' => true, 'message' => 'Đã Thêm Vào Giỏ Hàng']);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'error' => $e->getMessage()]);
        }
    }

    public function myCart(Request $request)
    {
        try {
            $id = $request->id;
            $data = GioHang::where('id_nguoi_dung', $id)->select('id')->with(['chiTietGioHang' => function ($query) {
                $query->with(["sanPham" => function ($query) {
                    $query->select('id', 'gia', 'khuyen_mai', 'ten_san_pham')->with(['hinhAnh' => function ($query) {
                        $query->select('id_san_pham', 'hinh_anh_san_pham')->first();
                    }]);
                }])->select('id', 'id_gio_hang', 'id_san_pham', 'so_luong');
            }])->first();
            return response()->json(['status' => true, 'data' => $data]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'error' => $e->getMessage()]);
        }
    }

    public function getPaymentAmount(Request $request)
    {
        try {
            $id = $request->id;
            $amount = 0;
            $data = GioHang::where('id_nguoi_dung', $id)->with(['chiTietGioHang' => function ($query) {
                $query->with('sanPham');
            }])->first();
            if (isset($data)) {
                if (isset($data->chiTietGioHang)) {
                    foreach ($data->chiTietGioHang as $item) {
                        $amount = $amount + ($item->so_luong * ($item->sanPham->gia - $item->sanPham->gia * ($item->sanPham->khuyen_mai / 100)));
                    }
                }
                $amount = $amount + $amount * 0.1 + 10;
                $amount = round($amount, 2);
                return response()->json(['status' => true, 'amount' => $amount]);
            }
            return response()->json(['status' => false]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'error' => $e->getMessage()]);
        }
    }

    public function createQRCodeZalo(Request $request)
    {
        $app_id = 1;
        $key1 = '8NdU5pG5R2spGHGhyO99HN1OhD8IQJBn';
        $key2 = 'uUfsWgfLkRLzq6W2uNXTCxrfxs51auny';

        // Tạo dữ liệu yêu cầu thanh toán
        $data = [
            'app_id' => $app_id,
            'app_trans_id' => uniqid(), // Mã giao dịch duy nhất
            'app_time' => time(),
            'app_user' => 'user123',
            'amount' => 10000, // Số tiền thanh toán
            'description' => 'Thanh toán đơn hàng',
        ];

        ksort($data);
        $signature = hash('sha256', implode('', $data) . $key2);

        $data['mac'] = $signature;

        $response = Http::post('https://sandbox.zalopay.com.vn/v001/tpe/createorder', $data);

        // Xác minh chữ ký của ZaloPay

        // Trả về QR code và thông tin thanh toán
        return $response->json();
    }
}
