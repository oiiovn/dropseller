<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\AdminDashboardService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function __construct(private readonly AdminDashboardService $adminDashboardService)
    {
    }

    public function __invoke(Request $request): JsonResponse
    {
        [$dateFrom, $dateTo] = $this->resolveDateRange($request);

        return response()->json($this->adminDashboardService->summary($dateFrom, $dateTo));
    }

    private function resolveDateRange(Request $request): array
    {
        if ($request->filled('month')) {
            $month = $request->string('month')->value();
            $start = Carbon::createFromFormat('Y-m', $month)->startOfMonth();

            return [$start->toDateString(), $start->copy()->endOfMonth()->toDateString()];
        }

        $dateFrom = $request->string('date_from')->value() ?: null;
        $dateTo = $request->string('date_to')->value() ?: null;

        return [$dateFrom, $dateTo];
    }
}
