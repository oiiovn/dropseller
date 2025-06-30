<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdminAccessController extends Controller
{
    /**
     * Xác thực mã truy cập admin và chuyển hướng đến trang admin
     */
    public function verifyAccess(Request $request)
    {
        $accessCode = $request->input('access_code');
        // Mã xác nhận cố định - bạn có thể thay đổi sau hoặc đặt trong env/config
        $validCode = config('admin.access_code', 'admin123');
        
        if ($accessCode === $validCode) {
            // Lưu trạng thái xác thực vào session
            session(['admin_verified' => true, 'admin_verified_at' => now()]);
            
            // Ghi log hoạt động
            Log::info("Admin access verified by user: " . auth()->id());
            
            // Chuyển hướng đến trang admin
            return redirect()->route('admin.dashboard')->with('success', 'Xác thực thành công');
        }
        
        // Ghi log thất bại
        Log::warning("Failed admin access attempt by user: " . auth()->id());
        
        // Chuyển hướng về trang trước đó với thông báo lỗi
        return back()->with('error', 'Mã xác nhận không hợp lệ. Vui lòng thử lại.');
    }
}
