<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsurePositiveBalance
{
   public function handle(Request $request, Closure $next)
{
    $user = auth()->user();

    if ($user && $user->total_amount < 0) {
        // Nếu đang ở dashboard rồi thì không redirect nữa
        if ($request->routeIs('dashboard')) {
            return $next($request);
        }

        return redirect()->route('dashboard')->with('error', 'Tài khoản của bạn đang bị âm. Vui lòng nạp tiền để tiếp tục.');
    }

    return $next($request);
}

}
