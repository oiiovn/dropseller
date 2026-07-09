<?php

namespace App\Services\Crm;

use App\Models\Crm\Affiliate;
use App\Models\Crm\Commission;
use App\Models\Crm\CommissionSettlement;
use App\Models\Crm\CommissionWallet;
use App\Models\Crm\Debt;
use App\Models\Crm\Order;
use App\Models\User;
use App\Support\Crm\CollaboratorScope;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class DashboardService
{
    private const TERMINAL_ORDER_STATUSES = ['completed', 'cancelled', 'refunded'];

    public function __construct(private readonly DebtAlertService $debtAlertService)
    {
    }

    public function summary(?User $user = null, ?string $dateFrom = null, ?string $dateTo = null): array
    {
        $orderQuery = $this->baseOrderQuery($user, $dateFrom, $dateTo);
        $debtQuery = $this->baseDebtQuery($user, $dateFrom, $dateTo);
        $commissionQuery = $this->baseCommissionQuery($user, $dateFrom, $dateTo);

        $overdueSummary = $this->debtAlertService->syncOverdueDebts();
        $totalRevenue = (float) (clone $orderQuery)->sum('total_amount_jpy');
        $totalDebt = (float) (clone $debtQuery)->sum('outstanding_jpy');

        $latestAlerts = $this->debtAlertService->latest(5);
        if ($user && CollaboratorScope::isCollaborator($user)) {
            $latestAlerts = collect($latestAlerts)->filter(function ($alert) use ($user) {
                return ($alert['affiliate_id'] ?? null) === $user->affiliate_id;
            })->values()->all();
        }

        $isCollaborator = $user && CollaboratorScope::isCollaborator($user);
        $wallet = ($isCollaborator && $user->affiliate_id)
            ? CommissionWallet::where('affiliate_id', $user->affiliate_id)->first()
            : null;

        $pendingOrdersQuery = (clone $orderQuery)->whereNotIn('order_status', self::TERMINAL_ORDER_STATUSES);

        return [
            'currency' => 'JPY',
            'scope' => ($user && CollaboratorScope::isPackaging($user))
                ? 'packaging'
                : ($isCollaborator ? 'collaborator' : 'global'),
            'filter' => [
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
            ],
            'total_orders' => (clone $orderQuery)->count(),
            'completed_orders' => (clone $orderQuery)->where('order_status', 'completed')->count(),
            'cancelled_orders' => (clone $orderQuery)->where('order_status', 'cancelled')->count(),
            'pending_orders_count' => (clone $pendingOrdersQuery)->count(),
            'total_revenue_jpy' => $totalRevenue,
            'total_profit_jpy' => (float) (clone $orderQuery)->sum('total_profit_jpy'),
            'outstanding_debt_jpy' => $totalDebt,
            'overdue_debt_count' => $overdueSummary['overdue_count'],
            'overdue_debt_amount_jpy' => $overdueSummary['overdue_amount_jpy'],
            'pending_commission_jpy' => $wallet
                ? (float) $wallet->unpaid_balance_jpy
                : (float) (clone $commissionQuery)
                    ->whereIn('wallet_status', ['credited', 'pending_settlement', 'pending_record'])
                    ->sum('commission_amount_jpy'),
            'paid_commission_jpy' => $wallet
                ? (float) $wallet->paid_balance_jpy
                : (float) (clone $commissionQuery)->where('wallet_status', 'settled')->sum('commission_amount_jpy'),
            'wallet_total_commission_jpy' => $wallet ? (float) $wallet->total_commission_jpy : null,
            'period_commission_jpy' => (float) (clone $commissionQuery)->sum('commission_amount_jpy'),
            'period_commission_count' => (clone $commissionQuery)->count(),
            'top_affiliates' => $this->topAffiliates($user, $dateFrom, $dateTo),
            'orders_by_status' => (clone $orderQuery)
                ->selectRaw('order_status, COUNT(*) as total')
                ->groupBy('order_status')
                ->pluck('total', 'order_status'),
            'pending_orders_by_status' => (clone $pendingOrdersQuery)
                ->selectRaw('order_status, COUNT(*) as total')
                ->groupBy('order_status')
                ->pluck('total', 'order_status'),
            'latest_alerts' => $latestAlerts,
            'affiliate' => ($isCollaborator && $user->affiliate_id)
                ? Affiliate::find($user->affiliate_id)
                : null,
            'revenue_trend' => $this->revenueTrend($user),
            'commission_trend' => $this->commissionTrend($user),
            'commission_overview' => $this->commissionOverview($user, $dateFrom, $dateTo),
            'recent_orders' => $this->recentOrders($user, $dateFrom, $dateTo),
        ];
    }

    private function baseOrderQuery(?User $user, ?string $dateFrom, ?string $dateTo): Builder
    {
        $query = Order::query();
        $this->applyUserScope($query, $user);
        $this->applyDateRange($query, $dateFrom, $dateTo);

        return $query;
    }

    private function baseDebtQuery(?User $user, ?string $dateFrom, ?string $dateTo): Builder
    {
        $query = Debt::query();

        if ($user && CollaboratorScope::isCollaborator($user)) {
            $affiliateId = CollaboratorScope::affiliateId($user);
            $query->whereHas('order', fn ($q) => $q->where('affiliate_id', $affiliateId));
        }

        if ($dateFrom || $dateTo) {
            $query->whereHas('order', function ($orderQuery) use ($user, $dateFrom, $dateTo) {
                $this->applyUserScope($orderQuery, $user);
                $this->applyDateRange($orderQuery, $dateFrom, $dateTo);
            });
        }

        return $query;
    }

    private function baseCommissionQuery(?User $user, ?string $dateFrom, ?string $dateTo): Builder
    {
        $query = Commission::query()->where('wallet_status', '!=', 'cancelled');

        if ($user && CollaboratorScope::isCollaborator($user)) {
            $query->where('affiliate_id', CollaboratorScope::affiliateId($user));
        }

        $this->applyCommissionDateRange($query, $dateFrom, $dateTo);

        return $query;
    }

    private function applyUserScope(Builder $query, ?User $user): void
    {
        if ($user && CollaboratorScope::isCollaborator($user)) {
            $query->where('affiliate_id', CollaboratorScope::affiliateId($user));
        }

        if ($user && CollaboratorScope::isPackaging($user)) {
            $query->whereIn('order_status', CollaboratorScope::PACKAGING_ORDER_STATUSES);
        }
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

    private function applyCommissionDateRange(Builder $query, ?string $dateFrom, ?string $dateTo): void
    {
        if ($dateFrom) {
            $query->whereDate('credited_at', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('credited_at', '<=', $dateTo);
        }
    }

    private function commissionOverview(?User $user, ?string $dateFrom, ?string $dateTo): ?array
    {
        if (! $user || ! CollaboratorScope::isCollaborator($user) || ! $user->affiliate_id) {
            return null;
        }

        $affiliateId = $user->affiliate_id;
        $wallet = CommissionWallet::firstOrCreate(
            ['affiliate_id' => $affiliateId],
            ['unpaid_balance_jpy' => 0, 'paid_balance_jpy' => 0, 'total_commission_jpy' => 0]
        );

        $commissionQuery = Commission::query()
            ->where('affiliate_id', $affiliateId)
            ->where('wallet_status', '!=', 'cancelled');
        $this->applyCommissionDateRange($commissionQuery, $dateFrom, $dateTo);

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

        $pendingSettlements = CommissionSettlement::query()
            ->where('affiliate_id', $affiliateId)
            ->where('status', 'pending');

        return [
            'wallet_unpaid_balance_jpy' => (float) $wallet->unpaid_balance_jpy,
            'wallet_paid_balance_jpy' => (float) $wallet->paid_balance_jpy,
            'wallet_total_commission_jpy' => (float) $wallet->total_commission_jpy,
            'period_commission_jpy' => (float) (clone $commissionQuery)->sum('commission_amount_jpy'),
            'period_commission_count' => (clone $commissionQuery)->count(),
            'pending_settlements_count' => (clone $pendingSettlements)->count(),
            'pending_settlements_amount_jpy' => (float) (clone $pendingSettlements)->sum('total_amount_jpy'),
            'by_wallet_status' => $byStatus,
            'recent_commissions' => $this->recentCommissions($affiliateId, $dateFrom, $dateTo),
        ];
    }

    private function recentCommissions(int $affiliateId, ?string $dateFrom, ?string $dateTo): array
    {
        return Commission::query()
            ->with(['order:id,order_code'])
            ->where('affiliate_id', $affiliateId)
            ->where('wallet_status', '!=', 'cancelled')
            ->when($dateFrom, fn ($q) => $q->whereDate('credited_at', '>=', $dateFrom))
            ->when($dateTo, fn ($q) => $q->whereDate('credited_at', '<=', $dateTo))
            ->latest('credited_at')
            ->limit(8)
            ->get(['id', 'order_id', 'commission_amount_jpy', 'applied_rule_name', 'wallet_status', 'credited_at'])
            ->map(fn (Commission $c) => [
                'id' => $c->id,
                'order_id' => $c->order_id,
                'order_code' => $c->order?->order_code,
                'commission_amount_jpy' => (float) $c->commission_amount_jpy,
                'applied_rule_name' => $c->applied_rule_name,
                'wallet_status' => $c->wallet_status,
                'credited_at' => $c->credited_at?->toIso8601String(),
            ])
            ->all();
    }

    private function recentOrders(?User $user, ?string $dateFrom, ?string $dateTo): array
    {
        return Order::query()
            ->with(['customer:id,full_name'])
            ->when($user && CollaboratorScope::isCollaborator($user), fn ($q) => $q->where('affiliate_id', $user->affiliate_id))
            ->when($user && CollaboratorScope::isPackaging($user), fn ($q) => $q->whereIn('order_status', CollaboratorScope::PACKAGING_ORDER_STATUSES))
            ->when($dateFrom, fn ($q) => $q->whereDate('created_at', '>=', $dateFrom))
            ->when($dateTo, fn ($q) => $q->whereDate('created_at', '<=', $dateTo))
            ->latest()
            ->limit(8)
            ->get(['id', 'order_code', 'order_status', 'payment_status', 'total_amount_jpy', 'customer_id', 'created_at'])
            ->map(fn (Order $order) => [
                'id' => $order->id,
                'order_code' => $order->order_code,
                'order_status' => $order->order_status,
                'payment_status' => $order->payment_status,
                'total_amount_jpy' => (float) $order->total_amount_jpy,
                'customer_name' => $order->customer?->full_name,
                'created_at' => $order->created_at?->toIso8601String(),
            ])
            ->all();
    }

    private function revenueTrend(?User $user): array
    {
        return $this->buildMonthlyTrend(function (array $month) use ($user) {
            $orders = Order::query()
                ->when($user && CollaboratorScope::isCollaborator($user), fn ($q) => $q->where('affiliate_id', $user->affiliate_id))
                ->when($user && CollaboratorScope::isPackaging($user), fn ($q) => $q->whereIn('order_status', CollaboratorScope::PACKAGING_ORDER_STATUSES))
                ->whereDate('created_at', '>=', $month['date_from'])
                ->whereDate('created_at', '<=', $month['date_to']);

            return [
                'period_key' => $month['period_key'],
                'label' => $month['label'],
                'orders' => (clone $orders)->count(),
                'revenue_jpy' => (float) (clone $orders)->sum('total_amount_jpy'),
                'completed_orders' => (clone $orders)->where('order_status', 'completed')->count(),
            ];
        });
    }

    private function commissionTrend(?User $user): array
    {
        if ($user && CollaboratorScope::isPackaging($user)) {
            return [];
        }

        return $this->buildMonthlyTrend(function (array $month) use ($user) {
            $commissions = Commission::query()
                ->where('wallet_status', '!=', 'cancelled')
                ->when($user && CollaboratorScope::isCollaborator($user), fn ($q) => $q->where('affiliate_id', $user->affiliate_id))
                ->whereDate('credited_at', '>=', $month['date_from'])
                ->whereDate('credited_at', '<=', $month['date_to']);

            return [
                'period_key' => $month['period_key'],
                'label' => $month['label'],
                'commission_count' => (clone $commissions)->count(),
                'commission_jpy' => (float) (clone $commissions)->sum('commission_amount_jpy'),
                'settled_jpy' => (float) (clone $commissions)->where('wallet_status', 'settled')->sum('commission_amount_jpy'),
            ];
        });
    }

    private function buildMonthlyTrend(callable $mapper): array
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

        return $months->map($mapper)->all();
    }

    private function topAffiliates(?User $user, ?string $dateFrom, ?string $dateTo): array
    {
        if ($user && CollaboratorScope::isCollaborator($user)) {
            return [];
        }

        if ($dateFrom || $dateTo) {
            return Order::query()
                ->whereNotNull('affiliate_id')
                ->when($dateFrom, fn ($query) => $query->whereDate('created_at', '>=', $dateFrom))
                ->when($dateTo, fn ($query) => $query->whereDate('created_at', '<=', $dateTo))
                ->groupBy('affiliate_id')
                ->selectRaw('affiliate_id')
                ->selectRaw('SUM(total_amount_jpy) as gross_sales_jpy')
                ->selectRaw('SUM(CASE WHEN order_status = ? THEN 1 ELSE 0 END) as successful_orders', ['completed'])
                ->orderByDesc('gross_sales_jpy')
                ->limit(5)
                ->get()
                ->map(function ($row) {
                    $affiliate = Affiliate::find($row->affiliate_id);

                    return [
                        'id' => $row->affiliate_id,
                        'code' => $affiliate?->code,
                        'full_name' => $affiliate?->full_name,
                        'gross_sales_jpy' => (float) $row->gross_sales_jpy,
                        'successful_orders' => (int) $row->successful_orders,
                    ];
                })
                ->all();
        }

        return Affiliate::query()
            ->orderByDesc('gross_sales_jpy')
            ->limit(5)
            ->get(['id', 'code', 'full_name', 'gross_sales_jpy', 'successful_orders'])
            ->all();
    }
}
