<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Services\Crm\DashboardService;
use App\Services\Crm\DebtAlertService;
use App\Services\Crm\ReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ReportController extends Controller
{
    public function __construct(
        private readonly DashboardService $dashboardService,
        private readonly ReportService $reportService,
        private readonly DebtAlertService $debtAlertService
    ) {}

    public function dashboard(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'month' => ['nullable', 'date_format:Y-m'],
        ]);

        [$dateFrom, $dateTo] = $this->resolveDateRange(
            $validated['month'] ?? null,
            $validated['date_from'] ?? null,
            $validated['date_to'] ?? null,
        );

        return response()->json($this->dashboardService->summary($request->user(), $dateFrom, $dateTo));
    }

    private function resolveDateRange(?string $month, ?string $dateFrom, ?string $dateTo): array
    {
        if ($month) {
            $start = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
            $end = $start->copy()->endOfMonth();

            return [$start->toDateString(), $end->toDateString()];
        }

        return [$dateFrom, $dateTo];
    }

    public function advanced(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'granularity' => ['nullable', 'in:week,month'],
            'periods' => ['nullable', 'integer', 'min:1', 'max:24'],
        ]);

        return response()->json(
            $this->reportService->advanced($validated['granularity'] ?? 'month', (int) ($validated['periods'] ?? 6))
        );
    }

    public function affiliatePerformance(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'granularity' => ['nullable', 'in:week,month'],
            'top' => ['nullable', 'integer', 'min:3', 'max:50'],
        ]);

        return response()->json(
            $this->reportService->affiliatePerformance($validated['granularity'] ?? 'month', (int) ($validated['top'] ?? 10))
        );
    }

    public function overdueAlerts(Request $request): JsonResponse
    {
        $limit = (int) $request->integer('limit', 10);

        return response()->json([
            'summary' => $this->debtAlertService->syncOverdueDebts(),
            'alerts' => $this->debtAlertService->latest(max(1, min($limit, 50))),
        ]);
    }
}
