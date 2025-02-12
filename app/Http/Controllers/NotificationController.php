<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $Notification = Notification::where('user_id', Auth::id())->orderBy('created_at', 'desc')->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'shop_id' => 'required|exists:shops_name,shop_id',
            'title' => 'required',
            'message' => 'required',
        ]);

        $notification = Notification::create($request->all());
        return response()->json($notification, 201);
    }
    public function markRead(Request $request)
    {
        $user = Auth::user();
        if (!$user->id) {
            return response()->json(['error' => 'User not found'], 400);
        }

        // Cập nhật tất cả thông báo chưa đọc thành đã đọc
        $notifications = Notification::where('user_id', $user->id)
            ->where('is_read', 0) // Lọc thông báo chưa đọc
            ->update(['is_read' => 1]);

        // Trả về phản hồi xác nhận
        return response()->json(['success' => 'Notifications marked as read']);
    }
}
