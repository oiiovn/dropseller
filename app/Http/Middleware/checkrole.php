<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|array  ...$roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect('login');
        }

        $userId = Auth::id();
        
        // Không cần kiểm tra nếu không có vai trò nào được yêu cầu
        if (empty($roles)) {
            return $next($request);
        }
        
        // Nếu roles được truyền dưới dạng chuỗi phân cách bằng dấu phẩy, chuyển nó thành mảng
        if (is_string($roles[0]) && str_contains($roles[0], ',')) {
            $roles = explode(',', $roles[0]);
        }
        
        // Kiểm tra trực tiếp trong database thay vì thông qua model
        $hasRole = DB::table('role_user')
            ->join('roles', 'role_user.role_id', '=', 'roles.id')
            ->where('role_user.user_id', $userId)
            ->whereIn('roles.slug', $roles)
            ->exists();
        
        if ($hasRole) {
            return $next($request);
        }
        
        return redirect()->back()->with('error', 'Bạn không có quyền truy cập chức năng này.');
    }
}
