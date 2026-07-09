<?php

namespace App\Services\Crm;

use App\Models\Crm\Affiliate;
use App\Models\Crm\Commission;
use App\Models\Crm\CommissionAdjustment;
use App\Models\Crm\CommissionSettlement;
use App\Models\Crm\CommissionWallet;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CommissionWalletService
{
    public function ensureWallet(int $affiliateId): CommissionWallet
    {
        return CommissionWallet::firstOrCreate(
            ['affiliate_id' => $affiliateId],
            [
                'unpaid_balance_jpy' => 0,
                'paid_balance_jpy' => 0,
                'total_commission_jpy' => 0,
            ]
        );
    }

    public function credit(int $affiliateId, float $amount): CommissionWallet
    {
        return DB::transaction(function () use ($affiliateId, $amount) {
            $wallet = $this->ensureWallet($affiliateId);
            $wallet->increment('unpaid_balance_jpy', $amount);
            $wallet->increment('total_commission_jpy', $amount);

            return $wallet->fresh();
        });
    }

    public function debitUnpaid(int $affiliateId, float $amount): CommissionWallet
    {
        return DB::transaction(function () use ($affiliateId, $amount) {
            $wallet = $this->ensureWallet($affiliateId);
            $wallet->update([
                'unpaid_balance_jpy' => max((float) $wallet->unpaid_balance_jpy - $amount, 0),
                'total_commission_jpy' => max((float) $wallet->total_commission_jpy - $amount, 0),
            ]);

            return $wallet->fresh();
        });
    }

    public function settlePaid(int $affiliateId, float $amount): CommissionWallet
    {
        return DB::transaction(function () use ($affiliateId, $amount) {
            $wallet = $this->ensureWallet($affiliateId);
            $wallet->update([
                'unpaid_balance_jpy' => max((float) $wallet->unpaid_balance_jpy - $amount, 0),
                'paid_balance_jpy' => (float) $wallet->paid_balance_jpy + $amount,
            ]);

            return $wallet->fresh();
        });
    }

    public function createClawback(Commission $original): CommissionAdjustment
    {
        return DB::transaction(function () use ($original) {
            $amount = -1 * abs((float) $original->commission_amount_jpy);

            $adjustment = CommissionAdjustment::create([
                'affiliate_id' => $original->affiliate_id,
                'commission_id' => $original->id,
                'amount_jpy' => $amount,
                'reason' => 'Bù trừ hoa hồng do đơn bị huỷ/hoàn sau khi đã quyết toán',
                'wallet_status' => 'credited',
            ]);

            $this->credit($original->affiliate_id, $amount);

            return $adjustment;
        });
    }

    public function summaryForAffiliate(int $affiliateId, ?string $periodMonth = null): array
    {
        $wallet = $this->ensureWallet($affiliateId);
        $periodMonth = $periodMonth ?? now()->format('Y-m');

        $monthQuery = Commission::query()
            ->where('affiliate_id', $affiliateId)
            ->where('wallet_status', '!=', 'cancelled')
            ->whereYear('credited_at', substr($periodMonth, 0, 4))
            ->whereMonth('credited_at', substr($periodMonth, 5, 2));

        return [
            'wallet' => $wallet->load('affiliate:id,code,full_name,phone'),
            'unpaid_balance_jpy' => (float) $wallet->unpaid_balance_jpy,
            'paid_balance_jpy' => (float) $wallet->paid_balance_jpy,
            'total_commission_jpy' => (float) $wallet->total_commission_jpy,
            'current_month_commission_jpy' => (float) (clone $monthQuery)->sum('commission_amount_jpy'),
            'current_month_order_count' => (clone $monthQuery)->distinct('order_id')->count('order_id'),
            'period_month' => $periodMonth,
        ];
    }

    public function listAffiliateSummaries(?int $affiliateId = null, array $filters = []): Collection
    {
        $periodMonth = $filters['period_month'] ?? now()->format('Y-m');

        $query = Affiliate::query()->with('commissionWallet');

        if ($affiliateId) {
            $query->where('id', $affiliateId);
        }

        if (! empty($filters['search'])) {
            $search = '%'.$filters['search'].'%';
            $query->where(function ($builder) use ($search) {
                $builder
                    ->where('full_name', 'like', $search)
                    ->orWhere('code', 'like', $search)
                    ->orWhere('phone', 'like', $search);
            });
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['area'])) {
            $query->where('area', $filters['area']);
        }

        $rows = $query->orderBy('full_name')->get()->map(function (Affiliate $affiliate) use ($periodMonth) {
            $summary = $this->summaryForAffiliate($affiliate->id, $periodMonth);
            $pendingSettlements = CommissionSettlement::query()
                ->where('affiliate_id', $affiliate->id)
                ->where('status', 'pending');

            return [
                'affiliate_id' => $affiliate->id,
                'code' => $affiliate->code,
                'full_name' => $affiliate->full_name,
                'phone' => $affiliate->phone,
                'area' => $affiliate->area,
                'status' => $affiliate->status,
                'unpaid_balance_jpy' => $summary['unpaid_balance_jpy'],
                'paid_balance_jpy' => $summary['paid_balance_jpy'],
                'total_commission_jpy' => $summary['total_commission_jpy'],
                'current_month_commission_jpy' => $summary['current_month_commission_jpy'],
                'current_month_order_count' => $summary['current_month_order_count'],
                'period_month' => $periodMonth,
                'pending_settlements_count' => (clone $pendingSettlements)->count(),
                'pending_settlements_amount_jpy' => (float) (clone $pendingSettlements)->sum('total_amount_jpy'),
            ];
        });

        if (! empty($filters['unpaid_only'])) {
            $rows = $rows->filter(fn (array $row) => $row['unpaid_balance_jpy'] > 0);
        }

        if (! empty($filters['has_pending_settlement'])) {
            $rows = $rows->filter(fn (array $row) => $row['pending_settlements_count'] > 0);
        }

        return $rows->values();
    }

    public function createSettlement(int $affiliateId, string $periodMonth, ?string $note = null): CommissionSettlement
    {
        return DB::transaction(function () use ($affiliateId, $periodMonth, $note) {
            $existing = CommissionSettlement::query()
                ->where('affiliate_id', $affiliateId)
                ->where('period_month', $periodMonth)
                ->first();

            if ($existing) {
                return $existing->fresh(['affiliate', 'commissions.order']);
            }

            $commissions = Commission::query()
                ->where('affiliate_id', $affiliateId)
                ->where('wallet_status', 'credited')
                ->whereYear('credited_at', substr($periodMonth, 0, 4))
                ->whereMonth('credited_at', substr($periodMonth, 5, 2))
                ->get();

            $total = (float) $commissions->sum('commission_amount_jpy');

            $settlement = CommissionSettlement::create([
                'affiliate_id' => $affiliateId,
                'period_month' => $periodMonth,
                'total_amount_jpy' => $total,
                'status' => 'pending',
                'note' => $note,
            ]);

            Commission::query()
                ->whereIn('id', $commissions->pluck('id'))
                ->update([
                    'wallet_status' => 'pending_settlement',
                    'settlement_id' => $settlement->id,
                ]);

            return $settlement->fresh(['affiliate', 'commissions.order']);
        });
    }

    public function markSettlementPaid(CommissionSettlement $settlement, int $userId, ?string $note = null): CommissionSettlement
    {
        if ($settlement->status === 'paid') {
            return $settlement;
        }

        return DB::transaction(function () use ($settlement, $userId, $note) {
            $amount = (float) $settlement->total_amount_jpy;

            Commission::query()
                ->where('settlement_id', $settlement->id)
                ->update([
                    'wallet_status' => 'settled',
                    'payment_status' => 'paid',
                    'paid_at' => now()->toDateString(),
                    'paid_by' => $userId,
                ]);

            $settlement->update([
                'status' => 'paid',
                'paid_at' => now()->toDateString(),
                'paid_by' => $userId,
                'note' => $note ?? $settlement->note,
            ]);

            $this->settlePaid($settlement->affiliate_id, $amount);

            return $settlement->fresh(['affiliate', 'commissions.order', 'payer']);
        });
    }
}
