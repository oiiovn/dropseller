<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OrderCheckController extends Controller
{
    public function sendMessage(Request $request)
    {
        $token = "7802275128:AAEPxYj6YL-e_dznMk23d_577ifDyXzWxLY"; // Token bot
        $chat_id = "6610486039"; // Chat ID cá nhân bạn

        // Lấy nội dung tin nhắn từ form gửi lên
        $message = $request->input('message');

        // Gửi tin nhắn lên Telegram
        file_get_contents("https://api.telegram.org/bot$token/sendMessage?chat_id=$chat_id&text=" . urlencode($message));

        // Redirect hoặc thông báo thành công
        return redirect()->back()->with('success', 'Đã gửi tin nhắn thành công!');
    }
}
