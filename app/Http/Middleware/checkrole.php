<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect('login');
        }

        $userId = Auth::id();

        // Nếu roles là 1 chuỗi "admin,seller", thì explode
        if (is_string($roles[0]) && str_contains($roles[0], ',')) {
            $roles = explode(',', $roles[0]);
        }

        // Chuyển slug role thành role_id nếu cần
        $roleIds = DB::table('roles')
            ->whereIn('slug', $roles)
            ->pluck('id')
            ->toArray();

        $hasRole = DB::table('role_user')
            ->where('user_id', $userId)
            ->whereIn('role_id', $roleIds)
            ->exists();

        if ($hasRole) {
            return $next($request);
        }

        // Nếu request là AJAX, trả lỗi JSON
        if ($request->ajax()) {
            return response()->json(['error' => 'Bạn không có quyền truy cập.'], 403);
        }

        return redirect()->route('dashboard')->with('error', 'Bạn không có quyền truy cập.');
    }
}
