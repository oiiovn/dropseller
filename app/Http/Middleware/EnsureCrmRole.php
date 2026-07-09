<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCrmRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $roles = ''): Response
    {
        $allowedRoles = collect(explode('|', $roles))->filter()->values()->all();

        if (!empty($allowedRoles) && ! $request->user()?->hasCrmAnyRole($allowedRoles)) {
            abort(403, 'Bạn không có quyền truy cập tài nguyên này.');
        }

        return $next($request);
    }
}
