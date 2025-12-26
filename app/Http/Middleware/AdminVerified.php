<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AdminVerified
{
    /**
     * Xử lý request sau khi kiểm tra xác thực admin.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Kiểm tra xem người dùng đã xác thực mã admin hay chưa
        if (!session('admin_verified')) {
            return redirect()->route('home')->with('error', 'Bạn cần xác thực mã admin để truy cập trang này.');
        }
        
        // Kiểm tra thời gian hết hạn
        $verifiedAt = session('admin_verified_at');
        $expirationMinutes = config('admin.access_expiration', 60);
        $expiresAt = Carbon::parse($verifiedAt)->addMinutes($expirationMinutes);
        
        if (now()->greaterThan($expiresAt)) {
            // Xóa session xác thực đã hết hạn
            session()->forget(['admin_verified', 'admin_verified_at']);
            return redirect()->route('home')->with('error', 'Phiên xác thực admin đã hết hạn. Vui lòng xác thực lại.');
        }
        
        return $next($request);
    }
}
