<?php

namespace App\Http\Middleware;

use App\Models\DebtCreditorActivityLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogDebtCreditorActivity
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        try {
            DebtCreditorActivityLog::logViewPage($request);
        } catch (\Throwable $e) {
            // Không làm vỡ request nếu log lỗi
            report($e);
        }

        return $response;
    }
}
