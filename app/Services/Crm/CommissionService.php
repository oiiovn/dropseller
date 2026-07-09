<?php

namespace App\Services\Crm;

use App\Models\Crm\Affiliate;
use App\Models\Crm\CommissionRule;
use App\Models\Crm\Commission;
use App\Models\Crm\CommissionPayout;
use App\Models\Crm\Order;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class CommissionService
{
    public function __construct(private readonly CommissionWalletService $walletService)
    {
    }

    public function generateFromOrder(Order $order, ?float $overrideRate = null): ?Commission
    {
        if (! $order->affiliate_id) {
            return null;
        }

        $existing = Commission::query()->where('order_id', $order->id)->first();
        if ($existing) {
            return $existing;
        }

        $order->loadMissing('items.product', 'affiliate');

        $rule = $overrideRate === null ? $this->resolveRule($order) : null;
        $rate = $overrideRate ?? (float) ($rule?->rate_percent ?? 5.0);
        $amount = ((float) $order->total_amount_jpy * $rate) / 100;

        return DB::transaction(function () use ($order, $rule, $rate, $amount) {
            $commission = Commission::create([
                'order_id' => $order->id,
                'affiliate_id' => $order->affiliate_id,
                'commission_rule_id' => $rule?->id,
                'commission_rate' => $rate,
                'commission_amount_jpy' => $amount,
                'applied_rule_name' => $rule?->name ?? 'Mặc định',
                'approval_status' => 'approved',
                'payment_status' => 'unpaid',
                'wallet_status' => 'credited',
                'completed_at' => now(),
                'credited_at' => now(),
            ]);

            $this->walletService->credit($order->affiliate_id, $amount);
            $this->refreshAffiliateMetrics($order->affiliate_id);

            return $commission;
        });
    }

    public function reverseForOrder(Order $order): void
    {
        $commission = Commission::query()->where('order_id', $order->id)->first();

        if (! $commission || $commission->wallet_status === 'cancelled') {
            return;
        }

        DB::transaction(function () use ($commission) {
            $amount = (float) $commission->commission_amount_jpy;

            if (in_array($commission->wallet_status, ['credited', 'pending_record', 'pending_settlement'], true)) {
                $this->walletService->debitUnpaid($commission->affiliate_id, $amount);
                $commission->update([
                    'wallet_status' => 'cancelled',
                    'payment_status' => 'refunded',
                    'notes' => trim(($commission->notes ?? '').' Huỷ hoa hồng do đơn bị huỷ/hoàn.'),
                ]);
            } elseif ($commission->wallet_status === 'settled') {
                $this->walletService->createClawback($commission);
                $commission->update([
                    'notes' => trim(($commission->notes ?? '').' Đã tạo bù trừ kỳ sau do đơn bị huỷ/hoàn.'),
                ]);
            }

            $this->refreshAffiliateMetrics($commission->affiliate_id);
        });
    }

    public function approve(Commission $commission, int $userId): Commission
    {
        $commission->update([
            'approval_status' => 'approved',
            'approved_by' => $userId,
            'approved_at' => now()->toDateString(),
        ]);

        return $commission->fresh();
    }

    public function payout(Commission $commission, array $payload, int $userId): Commission
    {
        return DB::transaction(function () use ($commission, $payload, $userId) {
            CommissionPayout::create([
                'commission_id' => $commission->id,
                'amount_jpy' => $payload['amount_jpy'],
                'paid_at' => $payload['paid_at'],
                'payment_method' => $payload['payment_method'] ?? 'bank_transfer',
                'reference_code' => $payload['reference_code'] ?? null,
                'recorded_by' => $userId,
                'note' => $payload['note'] ?? null,
            ]);

            $totalPaid = (float) $commission->payouts()->sum('amount_jpy');
            $isPaid = $totalPaid >= (float) $commission->commission_amount_jpy;

            $commission->update([
                'payment_status' => $isPaid ? 'paid' : 'partial',
                'wallet_status' => $isPaid ? 'settled' : $commission->wallet_status,
                'paid_at' => $isPaid ? ($payload['paid_at'] ?? now()->toDateString()) : null,
                'paid_by' => $userId,
            ]);

            if ($isPaid) {
                $this->walletService->settlePaid($commission->affiliate_id, (float) $commission->commission_amount_jpy);
            }

            $this->refreshAffiliateMetrics($commission->affiliate_id);

            return $commission->fresh();
        });
    }

    public function refreshAffiliateMetrics(int $affiliateId): void
    {
        $affiliate = Affiliate::find($affiliateId);

        if (! $affiliate) {
            return;
        }

        $wallet = $this->walletService->ensureWallet($affiliateId);

        $grossSales = (float) Order::query()
            ->where('affiliate_id', $affiliateId)
            ->where('order_status', 'completed')
            ->sum('total_amount_jpy');

        $successfulOrders = Order::query()
            ->where('affiliate_id', $affiliateId)
            ->where('order_status', 'completed')
            ->count();

        $affiliate->update([
            'gross_sales_jpy' => $grossSales,
            'successful_orders' => $successfulOrders,
            'total_commission_jpy' => (float) $wallet->total_commission_jpy,
            'paid_commission_jpy' => (float) $wallet->paid_balance_jpy,
            'pending_commission_jpy' => (float) $wallet->unpaid_balance_jpy,
            'total_referred_customers' => $affiliate->customers()->count(),
        ]);
    }

    public function syncRuleAffiliates(CommissionRule $rule, array $affiliateIds): void
    {
        $affiliateIds = array_values(array_unique(array_map('intval', $affiliateIds)));

        if ($affiliateIds === []) {
            $rule->affiliates()->sync([]);

            return;
        }

        DB::table('crm_commission_rule_affiliate')
            ->whereIn('affiliate_id', $affiliateIds)
            ->where('commission_rule_id', '!=', $rule->id)
            ->delete();

        $rule->affiliates()->sync($affiliateIds);
    }

    private function resolveRule(Order $order): ?CommissionRule
    {
        if (! $order->affiliate_id) {
            return $this->resolveDefaultRule();
        }

        $assignedRule = $this->activeRulesQuery()
            ->whereHas('affiliates', fn ($query) => $query->where('affiliates.id', $order->affiliate_id))
            ->orderBy('priority')
            ->orderByDesc('rate_percent')
            ->first();

        return $assignedRule ?? $this->resolveDefaultRule();
    }

    private function resolveDefaultRule(): ?CommissionRule
    {
        return $this->activeRulesQuery()
            ->whereDoesntHave('affiliates')
            ->orderBy('priority')
            ->orderByDesc('rate_percent')
            ->first();
    }

    private function activeRulesQuery(): Builder
    {
        $today = now()->toDateString();

        return CommissionRule::query()
            ->where('is_active', true)
            ->where(function ($query) use ($today) {
                $query->whereNull('effective_from')->orWhere('effective_from', '<=', $today);
            })
            ->where(function ($query) use ($today) {
                $query->whereNull('effective_to')->orWhere('effective_to', '>=', $today);
            });
    }
}
