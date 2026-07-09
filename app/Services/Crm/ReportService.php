<?php

namespace App\Services\Crm;

use App\Models\Crm\Affiliate;
use App\Models\Crm\Order;
use App\Models\Crm\Payment;
use Carbon\Carbon;

class ReportService
{
    public function advanced(string $granularity = 'month', int $periods = 6): array
    {
        $granularity = in_array($granularity, ['week', 'month'], true) ? $granularity : 'month';
        $periods = max(1, min($periods, 24));

        $end = now()->endOfDay();
        $start = $granularity === 'week'
            ? now()->startOfWeek()->subWeeks($periods - 1)
            : now()->startOfMonth()->subMonths($periods - 1);

        $orders = Order::query()
            ->whereBetween('created_at', [$start, $end])
            ->get(['created_at', 'order_status', 'total_amount_jpy', 'total_profit_jpy']);

        $payments = Payment::query()
            ->where('approval_status', 'approved')
            ->whereBetween('payment_date', [$start->toDateString(), $end->toDateString()])
            ->get(['payment_date', 'amount_jpy']);

        $buckets = $this->emptyBuckets($start, $periods, $granularity);

        foreach ($orders as $order) {
            $key = $this->periodKey(Carbon::parse($order->created_at), $granularity);
            if (! isset($buckets[$key])) {
                continue;
            }

            $buckets[$key]['orders'] += 1;
            $buckets[$key]['revenue_jpy'] += (float) $order->total_amount_jpy;
            $buckets[$key]['profit_jpy'] += (float) $order->total_profit_jpy;

            if ($order->order_status === 'completed') {
                $buckets[$key]['completed_orders'] += 1;
            }
            if ($order->order_status === 'cancelled') {
                $buckets[$key]['cancelled_orders'] += 1;
            }
        }

        foreach ($payments as $payment) {
            $key = $this->periodKey(Carbon::parse($payment->payment_date), $granularity);
            if (! isset($buckets[$key])) {
                continue;
            }
            $buckets[$key]['collected_jpy'] += (float) $payment->amount_jpy;
        }

        return [
            'granularity' => $granularity,
            'from' => $start->toDateString(),
            'to' => $end->toDateString(),
            'series' => array_values($buckets),
        ];
    }

    public function affiliatePerformance(string $granularity = 'month', int $top = 10): array
    {
        $granularity = in_array($granularity, ['week', 'month'], true) ? $granularity : 'month';
        $start = $granularity === 'week' ? now()->subWeeks(1) : now()->subMonth();

        $top = max(3, min($top, 50));

        $rows = Affiliate::query()
            ->withCount([
                'orders as period_orders' => fn ($query) => $query->whereBetween('created_at', [$start, now()]),
            ])
            ->withSum([
                'orders as period_sales_jpy' => fn ($query) => $query->whereBetween('created_at', [$start, now()]),
            ], 'total_amount_jpy')
            ->orderByDesc('period_sales_jpy')
            ->limit($top)
            ->get(['id', 'code', 'full_name', 'area']);

        return [
            'granularity' => $granularity,
            'from' => $start->toDateString(),
            'to' => now()->toDateString(),
            'top_affiliates' => $rows,
        ];
    }

    private function emptyBuckets(Carbon $start, int $periods, string $granularity): array
    {
        $result = [];

        for ($index = 0; $index < $periods; $index++) {
            $periodStart = $granularity === 'week'
                ? $start->copy()->addWeeks($index)
                : $start->copy()->addMonths($index);

            $key = $this->periodKey($periodStart, $granularity);
            $result[$key] = [
                'period_key' => $key,
                'label' => $granularity === 'week'
                    ? 'Tuần ' . $periodStart->format('W/Y')
                    : 'Tháng ' . $periodStart->format('m/Y'),
                'orders' => 0,
                'completed_orders' => 0,
                'cancelled_orders' => 0,
                'revenue_jpy' => 0,
                'profit_jpy' => 0,
                'collected_jpy' => 0,
            ];
        }

        return $result;
    }

    private function periodKey(Carbon $date, string $granularity): string
    {
        return $granularity === 'week' ? $date->format('o-\WW') : $date->format('Y-m');
    }
}
