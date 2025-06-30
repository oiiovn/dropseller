<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        // Nếu request là AJAX hoặc mong muốn phản hồi JSON, trả về phản hồi lỗi xác thực
        if ($request->expectsJson()) {
            return null;
        }

        // Ngăn chặn chuyển hướng đến /keep-alive và buộc chuyển hướng đến login
        if ($request->path() === 'keep-alive') {
            return route('login');
        }

        return route('login');
    }
}
