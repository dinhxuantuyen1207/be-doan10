<?php

namespace App\Http\Controllers;

use App\Events\Message;
use App\Models\Chat;
use Exception;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function message(Request $request)
    {
        $chat = Chat::create(['user_id' => $request->input('userId'), 'message' => $request->input('message'), 'to_id' => $request->input('toId')]);
        broadcast(new Message($chat->user_id, $chat));
        return [];
    }

    public function chatUser(Request $request)
    {
        try {
            if (!isset($request->id)) {
                return response()->json(["status" => false, "message" => "User Not Found"]);
            }
            $chat = Chat::where('user_id', $request->id)->orWhere('to_id', $request->id)->orderBy('created_at', 'asc')->get();
            return response()->json(['status' => true, 'data' => $chat]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'error' => $e->getMessage()]);
        }
    }

    public function chatAdmin()
    {
        try {
            $chat = Chat::where('user_id', 0)->orWhere('to_id', 0)->orderBy('created_at', 'asc')->get();
            return response()->json(['status' => true, 'data' => $chat]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'error' => $e->getMessage()]);
        }
    }
}
