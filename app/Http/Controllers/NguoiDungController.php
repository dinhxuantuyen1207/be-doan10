<?php

namespace App\Http\Controllers;

use App\Jobs\SendForgotPasswordEmail;
use App\Models\NguoiDung;
use Illuminate\Support\Facades\Validator;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Vonage\Client;
use Vonage\SMS\Message\SMS;
use Vonage\Client\Credentials\Basic;

class NguoiDungController extends Controller
{
    public function list()
    {
        $data = NguoiDung::get();
        return response()->json(['data' => $data]);
    }

    public function name()
    {
        $data = NguoiDung::select('id', 'ten_nguoi_dung')->get();
        return response()->json(['data' => $data]);
    }

    public function listAll(Request $request)
    {
        try {
            $pre_page = 20;
            $search = '';

            if (isset($request->pre_page)) {
                $pre_page = $request->pre_page;
            }

            $data_pre = NguoiDung::select('id', 'ten_nguoi_dung', 'tai_khoan', 'email', 'so_dien_thoai');

            if (isset($request->search)) {
                $search = $request->search;
                $data_pre->where(function ($query) use ($search) {
                    $query->where('ten_nguoi_dung', 'like', '%' . $search . '%')
                        ->orWhere('tai_khoan', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%')
                        ->orWhere('so_dien_thoai', 'like', '%' . $search . '%');
                });
            }

            $data_pre = $data_pre->paginate($pre_page);

            return response()->json(['status' => true, 'data' => $data_pre]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function profile(Request $request)
    {
        $id = $request->id;
        $data = NguoiDung::select('id', 'tai_khoan', 'ten_nguoi_dung', 'dia_chi', 'so_dien_thoai', 'email', 'anh_dai_dien')->find($id);
        if (isset($data)) {
            return response()->json(['status' => true, 'data' => $data]);
        }
        return response()->json(['status' => false]);
    }

    public function update(Request $request)
    {
        try {
            $taikhoan = NguoiDung::find($request->id);
            if (isset($taikhoan)) {
                if (!empty($request->ten_nguoi_dung)) {
                    $taikhoan->ten_nguoi_dung = $request->ten_nguoi_dung;
                }
                if (!empty($request->dia_chi)) {
                    $taikhoan->dia_chi = $request->dia_chi;
                }
                if (!empty($request->so_dien_thoai)) {
                    $taikhoan->so_dien_thoai = $request->so_dien_thoai;
                }
                if (!empty($request->email)) {
                    $taikhoan->email = $request->email;
                }
                if (!empty($request->anh_dai_dien) && $request->anh_dai_dien != "\"\"") {
                    $file = $request->anh_dai_dien;
                    $name = time() . rand(1, 100) . "." . $file->getClientOriginalExtension();
                    $file->move('upload', $name);
                    $taikhoan->anh_dai_dien = $name;
                }
                $taikhoan->save();
                return response()->json(['status' => true]);
            } else {
                return response()->json(['status' => false]);
            }
        } catch (Exception $e) {
            return response()->json(['status' => false]);
        }
    }


    public function changePassword(Request $request)
    {
        try {
            $taikhoan = NguoiDung::find($request->id);
            $taikhoan->mat_khau = $request->mat_khau;
            $taikhoan['mat_khau'] = bcrypt($taikhoan['mat_khau']);
            $taikhoan->save();
            return response()->json(['status' => true]);
        } catch (Exception $e) {
            return response()->json(['status' => false]);
        }
    }

    public function destroy(Request $request)
    {
        try {
            $taikhoan = NguoiDung::find($request->id);
            if ($taikhoan) {
                $taikhoan->delete();
                return response()->json(['status' => true]);
            }
            return response()->json(['status' => false]);
        } catch (Exception $e) {
            return response()->json(['status' => false]);
        }
    }

    public function create(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'tai_khoan' => ['required', 'regex:/^[a-z0-9]{3,16}$/'],
                'mat_khau' => ['required', 'regex:/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])[0-9a-zA-Z]{8,20}$/'],
                'ten_nguoi_dung' => 'required',
                'email' => ['required', 'regex:/^[^\s@]+@[^\s@]+\.[^\s@]+$/'],
                'dia_chi' => 'required',
                'so_dien_thoai' => ['required', 'regex:/^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$/im'],
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => false, 'message' => 'Sign Up Error !']);
            }
            $check_name = NguoiDung::where('tai_khoan', $request->tai_khoan)->first();
            if (isset($check_name)) {
                return response()->json(['status' => false, 'message' => 'User already exists']);
            }
            $check_mail = NguoiDung::where('email', $request->email)->first();
            if (isset($check_mail)) {
                return response()->json(['status' => false, 'message' => 'Email already exists']);
            }
            $data = $request->all();
            $data['mat_khau'] = bcrypt($data['mat_khau']);
            NguoiDung::create($data);
            return response()->json(['status' => true]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false]);
        }
    }

    public function login(Request $request)
    {
        $mat_khau = $request->mat_khau;
        $tai_khoan = $request->tai_khoan;
        $user = NguoiDung::where('tai_khoan', $tai_khoan)->first();
        if ($user) {
            if (password_verify($mat_khau, $user->mat_khau)) {
                session(['id_user' => $user->id]);
                return response()->json(['status' => true, 'id' => $user->id, 'name' => $user->ten_nguoi_dung, 'avatar' => $user->anh_dai_dien]);
            } else {
                return response()->json(['status' => false]);
            }
        } else {
            return response()->json(['status' => false]);
        }
    }

    public function forgotPassword(Request $request)
    {
        try {
            if (!isset($request->id)) {
                return response()->json(['status' => false]);
            }
            $user_name = '';
            $user = NguoiDung::find($request->id);
            if (isset($user)) {
                $user_name = $user->ten_nguoi_dung;
                $password = Str::random(9);
                $password .= rand(0, 9);
                $password .= chr(rand(65, 90));
                $password = str_shuffle($password);
                $name = $password;
                SendForgotPasswordEmail::dispatch($user)->onQueue('emails');
                $basic  = new \Vonage\Client\Credentials\Basic("b132e30f", "CZiuMrF99fLeIfw2");
                $client = new \Vonage\Client($basic);
                $response = $client->sms()->send(
                    new \Vonage\SMS\Message\SMS("0898433473", "BRAND_NAME", 'A text message sent using the Nexmo SMS API')
                );
                $message = $response->current();
                if ($message->getStatus() == 0) {
                    echo "The message was sent successfully\n";
                } else {
                    echo "The message failed with status: " . $message->getStatus() . "\n";
                }
                $user['mat_khau'] = bcrypt($password);
                $user->save();
            }
            return response()->json(['status' => true]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'error' => $e->getMessage()]);
        }
    }

    public function getEmail(Request $request)
    {
        try {
            if (!isset($request->username)) {
                return response()->json(['status' => false]);
            }
            $data = NguoiDung::where('tai_khoan', $request->username)->select('id', 'email', 'so_dien_thoai')->first();
            if ($data) {
                return response()->json(['status' => true, 'data' => $data]);
            }
            return response()->json(['status' => false]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'error' => $e->getMessage()]);
        }
    }
}
