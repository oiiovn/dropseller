<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureDebtCodeVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (!$user || !$user->isDebtSystemUser()) {
            return redirect()->route('dashboard');
        }
        if (!session('debt_code_verified_at')) {
            return redirect()->route('debt.code.verify');
        }
        return $next($request);
    }
}
