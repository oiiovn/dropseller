<?php

namespace App\Services\Crm;

use App\Models\Crm\Order;
use App\Models\Crm\OrderStatusHistory;
use App\Models\User;

class OrderAutoCompleteService
{
    public function __construct(
        private readonly CrmNotificationService $notificationService,
        private readonly CommissionService $commissionService,
    ) {
    }

    public function isFullyPaid(Order $order): bool
    {
        if ($order->payment_status === 'paid') {
            return true;
        }

        $order->loadMissing('debt');

        return $order->debt && (float) $order->debt->outstanding_jpy <= 0;
    }

    public function tryComplete(Order $order, User $actor, ?string $note = null): Order
    {
        $order = $order->fresh(['debt']);

        if ($order->order_status !== 'delivered' || ! $this->isFullyPaid($order)) {
            return $order;
        }

        $fromStatus = $order->order_status;
        $order->update(['order_status' => 'completed']);

        OrderStatusHistory::create([
            'order_id' => $order->id,
            'from_status' => $fromStatus,
            'to_status' => 'completed',
            'changed_by' => $actor->id,
            'note' => $note ?? 'Tự động hoàn thành: đã giao hàng và thanh toán đủ',
        ]);

        $order = $order->fresh();
        $this->notificationService->orderStatusChanged($order, $fromStatus, 'completed', $actor);
        $this->commissionService->generateFromOrder($order);

        return $order;
    }
}
