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

    public function markAsRead($id)
    {
        $notification = Notification::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $notification->update(['is_read' => true]);
        return response()->json(['message' => 'Notification marked as read']);
    }
}