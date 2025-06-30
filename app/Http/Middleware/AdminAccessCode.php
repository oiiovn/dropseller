<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\AdminController;

class AdminAccessCode
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Kiểm tra xem admin đã xác thực mã truy cập chưa
        if (!AdminController::isAdminAccessVerified()) {
            // Nếu chưa xác thực, chuyển hướng tới trang kiểm tra mã
            return redirect()->route('admin.check_code')
                ->with([
                    'access_code_status' => 'error',
                    'access_code_message' => 'Vui lòng nhập mã truy cập để tiếp tục.'
                ]);
        }
        
        return $next($request);
    }
}
