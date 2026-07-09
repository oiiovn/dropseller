<?php

namespace App\Services\Crm;

use App\Models\Crm\Alert;
use App\Models\Crm\Debt;

class DebtAlertService
{
    public function __construct(private readonly CrmNotificationService $notificationService)
    {
    }

    public function syncOverdueDebts(): array
    {
        $today = now()->toDateString();

        Debt::query()
            ->whereDate('due_date', '<', $today)
            ->where('outstanding_jpy', '>', 0)
            ->update(['debt_status' => 'overdue']);

        $overdueDebts = Debt::query()
            ->with(['order:id,order_code', 'customer:id,full_name'])
            ->where('debt_status', 'overdue')
            ->where('outstanding_jpy', '>', 0)
            ->get();

        foreach ($overdueDebts as $debt) {
            Alert::updateOrCreate(
                [
                    'type' => 'overdue_debt',
                    'related_type' => Debt::class,
                    'related_id' => $debt->id,
                ],
                [
                    'severity' => 'high',
                    'title' => 'Công nợ quá hạn',
                    'message' => sprintf(
                        'Đơn %s - Khách %s còn nợ %s円 (quá hạn %s).',
                        $debt->order?->order_code ?? ('#' . $debt->order_id),
                        $debt->customer?->full_name ?? ('#' . $debt->customer_id),
                        number_format((float) $debt->outstanding_jpy, 0, '.', ','),
                        optional($debt->due_date)->format('Y-m-d')
                    ),
                    'resolved_at' => null,
                ]
            );

            $this->notificationService->debtOverdue($debt);
        }

        Alert::query()
            ->where('type', 'overdue_debt')
            ->whereNotIn('related_id', $overdueDebts->pluck('id'))
            ->whereNull('resolved_at')
            ->update(['resolved_at' => now()]);

        return [
            'overdue_count' => $overdueDebts->count(),
            'overdue_amount_jpy' => (float) $overdueDebts->sum('outstanding_jpy'),
        ];
    }

    public function latest(int $limit = 10)
    {
        return Alert::query()
            ->where('type', 'overdue_debt')
            ->whereNull('resolved_at')
            ->latest()
            ->limit($limit)
            ->get()
            ->map(function (Alert $alert) {
                $affiliateId = null;

                if ($alert->related_type === Debt::class) {
                    $affiliateId = Debt::query()
                        ->with('order:id,affiliate_id')
                        ->find($alert->related_id)
                        ?->order
                        ?->affiliate_id;
                }

                return array_merge($alert->toArray(), ['affiliate_id' => $affiliateId]);
            });
    }
}
