<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckPickOrderAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Cho phép admin hoặc van.btd90@gmail.com
        // @phpstan-ignore-next-line
        if ($user->hasRole('admin') || $user->email === 'van.btd90@gmail.com') {
            return $next($request);
        }

        // Nếu không có quyền
        if ($request->ajax()) {
            return response()->json(['error' => 'Bạn không có quyền truy cập.'], 403);
        }

        return redirect()->route('dashboard')->with('error', 'Bạn không có quyền truy cập.');
    }
}
