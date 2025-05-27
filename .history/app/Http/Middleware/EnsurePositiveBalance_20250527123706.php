<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsurePositiveBalance
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        // Nếu người dùng đang âm tiền => chặn
        if ($user && $user->total_amount < 0) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['message' => 'Tài khoản của bạn đang bị âm. Vui lòng nạp tiền.'], 403);
            }

            return redirect()->route('dashboard')->with('error', 'Tài khoản của bạn đang bị âm. Vui lòng nạp tiền để tiếp tục.');
        }

        return $next($request);
    }
}
