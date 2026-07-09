<?php

namespace App\Services\Crm;

use App\Models\Crm\Order;
use Illuminate\Validation\ValidationException;

class OrderDeletionService
{
    public const PACKAGING_STATUSES = [
        'waiting_delivery',
        'packaged',
        'delivering',
        'delivered',
        'completed',
    ];

    public function canCollaboratorDelete(Order $order): bool
    {
        return ! $this->hasPackagingStarted($order) && ! $this->hasEffectivePayment($order);
    }

    public function hasPackagingStarted(Order $order): bool
    {
        return in_array($order->order_status, self::PACKAGING_STATUSES, true);
    }

    public function hasEffectivePayment(Order $order): bool
    {
        if ($order->getAttribute('has_effective_payment') !== null) {
            return (bool) $order->getAttribute('has_effective_payment');
        }

        if ($order->relationLoaded('payments')) {
            return $order->payments
                ->whereIn('approval_status', ['pending', 'approved'])
                ->isNotEmpty();
        }

        return $order->payments()
            ->whereIn('approval_status', ['pending', 'approved'])
            ->exists();
    }

    public function assertCollaboratorCanDelete(Order $order): void
    {
        if ($this->hasPackagingStarted($order)) {
            throw ValidationException::withMessages([
                'order' => 'Không thể xóa đơn đã chuyển sang bộ phận đóng gói.',
            ]);
        }

        if ($this->hasEffectivePayment($order)) {
            throw ValidationException::withMessages([
                'order' => 'Không thể xóa đơn đã có thanh toán chờ duyệt hoặc đã được kế toán xác nhận.',
            ]);
        }
    }
}
