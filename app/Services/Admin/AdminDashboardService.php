<?php

namespace App\Services\Admin;

use App\Models\Crm\Affiliate;
use App\Models\Crm\Commission;
use App\Models\Crm\CommissionSettlement;
use App\Models\Crm\CommissionWallet;
use App\Models\Crm\Customer;
use App\Models\Crm\Debt;
use App\Models\Crm\Order;
use App\Models\Crm\Payment;
use App\Models\Crm\Role;
use App\Models\User;
use App\Services\Crm\DebtAlertService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class AdminDashboardService
{
    private const TERMINAL_ORDER_STATUSES = ['completed', 'cancelled', 'refunded'];

    public function __construct(private readonly DebtAlertService $debtAlertService)
    {
    }

    public function summary(?string $dateFrom = null, ?string $dateTo = null): array
    {
        $overdueSummary = $this->debtAlertService->syncOverdueDebts();
        $orderQuery = $this->orderQuery($dateFrom, $dateTo);
        $debtQuery = $this->debtQuery($dateFrom, $dateTo);

        $totalOrders = (clone $orderQuery)->count();
        $completedOrders = (clone $orderQuery)->where('order_status', 'completed')->count();
        $pendingOrdersQuery = (clone $orderQuery)->whereNotIn('order_status', self::TERMINAL_ORDER_STATUSES);

        $ordersByStatus = (clone $orderQuery)
            ->selectRaw('order_status, COUNT(*) as total')
            ->groupBy('order_status')
            ->pluck('total', 'order_status');

        $pendingByStatus = (clone $pendingOrdersQuery)
            ->selectRaw('order_status, COUNT(*) as total')
            ->groupBy('order_status')
            ->pluck('total', 'order_status');

        return [
            'currency' => 'JPY',
            'filter' => [
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
            ],
            'total_users' => User::count(),
            'total_affiliates' => Affiliate::count(),
            'active_affiliates' => Affiliate::where('status', 'active')->count(),
            'total_customers' => Customer::count(),
            'total_orders' => $totalOrders,
            'completed_orders' => $completedOrders,
            'cancelled_orders' => (clone $orderQuery)->where('order_status', 'cancelled')->count(),
            'pending_orders_count' => (clone $pendingOrdersQuery)->count(),
            'pending_orders_by_status' => $pendingByStatus,
            'total_revenue_jpy' => (float) (clone $orderQuery)->sum('total_amount_jpy'),
            'total_profit_jpy' => (float) (clone $orderQuery)->sum('total_profit_jpy'),
            'collected_revenue_jpy' => $this->collectedRevenue($dateFrom, $dateTo),
            'outstanding_debt_jpy' => (float) (clone $debtQuery)->sum('outstanding_jpy'),
            'overdue_debt_count' => $overdueSummary['overdue_count'],
            'overdue_debt_amount_jpy' => $overdueSummary['overdue_amount_jpy'],
            'pending_payments_count' => Payment::query()
                ->where('approval_status', 'pending')
                ->when($dateFrom || $dateTo, function ($query) use ($dateFrom, $dateTo) {
                    $query->whereHas('order', function ($orderQuery) use ($dateFrom, $dateTo) {
                        $this->applyDateRange($orderQuery, $dateFrom, $dateTo);
                    });
                })
                ->count(),
            'effective_affiliates_count' => $this->effectiveAffiliatesCount($dateFrom, $dateTo),
            'top_affiliates' => $this->topAffiliates($dateFrom, $dateTo),
            'recent_pending_orders' => $this->recentPendingOrders($dateFrom, $dateTo),
            'orders_by_status' => $ordersByStatus,
            'revenue_trend' => $this->revenueTrend(),
            'latest_alerts' => $this->debtAlertService->latest(6),
            'roles' => Role::query()->withCount('users')->get(['id', 'name', 'display_name']),
            'commission_overview' => $this->commissionOverview($dateFrom, $dateTo),
        ];
    }

    private function commissionOverview(?string $dateFrom, ?string $dateTo): array
    {
        $commissionQuery = $this->commissionQuery($dateFrom, $dateTo);

        $byStatus = (clone $commissionQuery)
            ->selectRaw('wallet_status, COUNT(*) as total_count, SUM(commission_amount_jpy) as total_amount')
            ->groupBy('wallet_status')
            ->get()
            ->mapWithKeys(fn ($row) => [
                $row->wallet_status => [
                    'count' => (int) $row->total_count,
                    'amount_jpy' => (float) $row->total_amount,
                ],
            ])
            ->all();

        $pendingSettlementsQuery = CommissionSettlement::query()->where('status', 'pending');

        return [
            'wallet_unpaid_balance_jpy' => (float) CommissionWallet::sum('unpaid_balance_jpy'),
            'wallet_paid_balance_jpy' => (float) CommissionWallet::sum('paid_balance_jpy'),
            'wallet_total_commission_jpy' => (float) CommissionWallet::sum('total_commission_jpy'),
            'period_commission_jpy' => (float) (clone $commissionQuery)->sum('commission_amount_jpy'),
            'period_commission_count' => (clone $commissionQuery)->count(),
            'pending_settlements_count' => (clone $pendingSettlementsQuery)->count(),
            'pending_settlements_amount_jpy' => (float) (clone $pendingSettlementsQuery)->sum('total_amount_jpy'),
            'by_wallet_status' => $byStatus,
            'top_affiliates' => $this->topCommissionAffiliates($dateFrom, $dateTo),
            'recent_commissions' => $this->recentCommissions($dateFrom, $dateTo),
            'trend' => $this->commissionTrend(),
        ];
    }

    private function commissionQuery(?string $dateFrom, ?string $dateTo): Builder
    {
        $query = Commission::query()->where('wallet_status', '!=', 'cancelled');

        if ($dateFrom) {
            $query->whereDate('credited_at', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('credited_at', '<=', $dateTo);
        }

        return $query;
    }

    private function topCommissionAffiliates(?string $dateFrom, ?string $dateTo): array
    {
        return Commission::query()
            ->where('wallet_status', '!=', 'cancelled')
            ->when($dateFrom, fn ($query) => $query->whereDate('credited_at', '>=', $dateFrom))
            ->when($dateTo, fn ($query) => $query->whereDate('credited_at', '<=', $dateTo))
            ->groupBy('affiliate_id')
            ->selectRaw('affiliate_id')
            ->selectRaw('COUNT(*) as commission_count')
            ->selectRaw('SUM(commission_amount_jpy) as commission_amount_jpy')
            ->selectRaw("SUM(CASE WHEN wallet_status = 'settled' THEN commission_amount_jpy ELSE 0 END) as settled_amount_jpy")
            ->orderByDesc('commission_amount_jpy')
            ->limit(8)
            ->get()
            ->map(function ($row) {
                $affiliate = Affiliate::find($row->affiliate_id);
                $wallet = $affiliate ? CommissionWallet::where('affiliate_id', $affiliate->id)->first() : null;

                return [
                    'id' => $row->affiliate_id,
                    'code' => $affiliate?->code,
                    'full_name' => $affiliate?->full_name,
                    'commission_count' => (int) $row->commission_count,
                    'commission_amount_jpy' => (float) $row->commission_amount_jpy,
                    'settled_amount_jpy' => (float) $row->settled_amount_jpy,
                    'unpaid_balance_jpy' => (float) ($wallet?->unpaid_balance_jpy ?? 0),
                ];
            })
            ->values()
            ->all();
    }

    private function recentCommissions(?string $dateFrom, ?string $dateTo): array
    {
        return Commission::query()
            ->with([
                'affiliate:id,code,full_name',
                'order:id,order_code',
            ])
            ->where('wallet_status', '!=', 'cancelled')
            ->when($dateFrom, fn ($query) => $query->whereDate('credited_at', '>=', $dateFrom))
            ->when($dateTo, fn ($query) => $query->whereDate('credited_at', '<=', $dateTo))
            ->latest('credited_at')
            ->limit(10)
            ->get([
                'id',
                'order_id',
                'affiliate_id',
                'commission_amount_jpy',
                'applied_rule_name',
                'wallet_status',
                'credited_at',
            ])
            ->map(fn (Commission $commission) => [
                'id' => $commission->id,
                'order_id' => $commission->order_id,
                'order_code' => $commission->order?->order_code,
                'affiliate_code' => $commission->affiliate?->code,
                'affiliate_name' => $commission->affiliate?->full_name,
                'commission_amount_jpy' => (float) $commission->commission_amount_jpy,
                'applied_rule_name' => $commission->applied_rule_name,
                'wallet_status' => $commission->wallet_status,
                'credited_at' => $commission->credited_at?->toIso8601String(),
            ])
            ->all();
    }

    private function commissionTrend(): array
    {
        $months = collect(range(5, 0))->map(function ($offset) {
            $date = Carbon::now()->startOfMonth()->subMonths($offset);

            return [
                'period_key' => $date->format('Y-m'),
                'label' => 'T'.$date->format('n').'/'.$date->format('Y'),
                'date_from' => $date->toDateString(),
                'date_to' => $date->copy()->endOfMonth()->toDateString(),
            ];
        });

        return $months->map(function (array $month) {
            $commissions = Commission::query()
                ->where('wallet_status', '!=', 'cancelled')
                ->whereDate('credited_at', '>=', $month['date_from'])
                ->whereDate('credited_at', '<=', $month['date_to']);

            return [
                'period_key' => $month['period_key'],
                'label' => $month['label'],
                'commission_count' => (clone $commissions)->count(),
                'commission_jpy' => (float) (clone $commissions)->sum('commission_amount_jpy'),
                'settled_jpy' => (float) (clone $commissions)->where('wallet_status', 'settled')->sum('commission_amount_jpy'),
            ];
        })->all();
    }

    private function orderQuery(?string $dateFrom, ?string $dateTo): Builder
    {
        $query = Order::query();
        $this->applyDateRange($query, $dateFrom, $dateTo);

        return $query;
    }

    private function debtQuery(?string $dateFrom, ?string $dateTo): Builder
    {
        $query = Debt::query()->where('outstanding_jpy', '>', 0);

        if ($dateFrom || $dateTo) {
            $query->whereHas('order', function ($orderQuery) use ($dateFrom, $dateTo) {
                $this->applyDateRange($orderQuery, $dateFrom, $dateTo);
            });
        }

        return $query;
    }

    private function applyDateRange(Builder $query, ?string $dateFrom, ?string $dateTo): void
    {
        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }
    }

    private function collectedRevenue(?string $dateFrom, ?string $dateTo): float
    {
        $query = Payment::query()->where('approval_status', 'approved');

        if ($dateFrom) {
            $query->whereDate('payment_date', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('payment_date', '<=', $dateTo);
        }

        return (float) $query->sum('amount_jpy');
    }

    private function effectiveAffiliatesCount(?string $dateFrom, ?string $dateTo): int
    {
        return Order::query()
            ->whereNotNull('affiliate_id')
            ->where('order_status', 'completed')
            ->when($dateFrom, fn ($query) => $query->whereDate('created_at', '>=', $dateFrom))
            ->when($dateTo, fn ($query) => $query->whereDate('created_at', '<=', $dateTo))
            ->distinct('affiliate_id')
            ->count('affiliate_id');
    }

    private function topAffiliates(?string $dateFrom, ?string $dateTo): array
    {
        return Order::query()
            ->whereNotNull('affiliate_id')
            ->when($dateFrom, fn ($query) => $query->whereDate('created_at', '>=', $dateFrom))
            ->when($dateTo, fn ($query) => $query->whereDate('created_at', '<=', $dateTo))
            ->groupBy('affiliate_id')
            ->selectRaw('affiliate_id')
            ->selectRaw('COUNT(*) as total_orders')
            ->selectRaw('SUM(total_amount_jpy) as gross_sales_jpy')
            ->selectRaw('SUM(CASE WHEN order_status = ? THEN 1 ELSE 0 END) as successful_orders', ['completed'])
            ->orderByDesc('gross_sales_jpy')
            ->limit(8)
            ->get()
            ->map(function ($row) {
                $affiliate = Affiliate::find($row->affiliate_id);
                $totalOrders = (int) $row->total_orders;
                $successfulOrders = (int) $row->successful_orders;

                return [
                    'id' => $row->affiliate_id,
                    'code' => $affiliate?->code,
                    'full_name' => $affiliate?->full_name,
                    'status' => $affiliate?->status,
                    'area' => $affiliate?->area,
                    'total_orders' => $totalOrders,
                    'gross_sales_jpy' => (float) $row->gross_sales_jpy,
                    'successful_orders' => $successfulOrders,
                    'success_rate' => $totalOrders > 0 ? round(($successfulOrders / $totalOrders) * 100, 1) : 0,
                ];
            })
            ->values()
            ->all();
    }

    private function recentPendingOrders(?string $dateFrom, ?string $dateTo): array
    {
        return Order::query()
            ->with([
                'customer:id,full_name,phone',
                'affiliate:id,code,full_name',
            ])
            ->whereNotIn('order_status', self::TERMINAL_ORDER_STATUSES)
            ->when($dateFrom, fn ($query) => $query->whereDate('created_at', '>=', $dateFrom))
            ->when($dateTo, fn ($query) => $query->whereDate('created_at', '<=', $dateTo))
            ->latest()
            ->limit(10)
            ->get([
                'id',
                'order_code',
                'order_status',
                'payment_status',
                'total_amount_jpy',
                'customer_id',
                'affiliate_id',
                'created_at',
            ])
            ->map(fn (Order $order) => [
                'id' => $order->id,
                'order_code' => $order->order_code,
                'order_status' => $order->order_status,
                'payment_status' => $order->payment_status,
                'total_amount_jpy' => (float) $order->total_amount_jpy,
                'customer_name' => $order->customer?->full_name,
                'affiliate_code' => $order->affiliate?->code,
                'affiliate_name' => $order->affiliate?->full_name,
                'created_at' => $order->created_at?->toIso8601String(),
            ])
            ->all();
    }

    private function revenueTrend(): array
    {
        $months = collect(range(5, 0))->map(function ($offset) {
            $date = Carbon::now()->startOfMonth()->subMonths($offset);

            return [
                'period_key' => $date->format('Y-m'),
                'label' => 'T'.$date->format('n').'/'.$date->format('Y'),
                'date_from' => $date->toDateString(),
                'date_to' => $date->copy()->endOfMonth()->toDateString(),
            ];
        });

        return $months->map(function (array $month) {
            $orders = Order::query()
                ->whereDate('created_at', '>=', $month['date_from'])
                ->whereDate('created_at', '<=', $month['date_to']);

            return [
                'period_key' => $month['period_key'],
                'label' => $month['label'],
                'orders' => (clone $orders)->count(),
                'revenue_jpy' => (float) (clone $orders)->sum('total_amount_jpy'),
                'completed_orders' => (clone $orders)->where('order_status', 'completed')->count(),
            ];
        })->all();
    }
}
