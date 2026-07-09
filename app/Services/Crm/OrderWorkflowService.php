<?php

namespace App\Services\Crm;

use App\Models\Crm\Order;
use App\Models\Crm\OrderStatusHistory;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderWorkflowService
{
    public function __construct(
        private readonly CrmNotificationService $notificationService,
        private readonly OrderAutoCompleteService $orderAutoCompleteService,
    ) {
    }

    private const ALL_STATUSES = [
        'new',
        'consulting',
        'confirmed',
        'deposit_pending',
        'deposit_paid',
        'waiting_delivery',
        'packaged',
        'delivering',
        'delivered',
        'completed',
        'cancelled',
        'refunded',
    ];

    private const CTV_TRANSITIONS = [
        'new' => ['consulting', 'cancelled'],
        'consulting' => ['confirmed', 'cancelled'],
        'confirmed' => ['deposit_pending', 'waiting_delivery', 'cancelled'],
        'deposit_pending' => ['waiting_delivery', 'cancelled'],
        'deposit_paid' => ['waiting_delivery'],
    ];

    private const STAFF_TRANSITIONS = [
        'new' => ['consulting', 'confirmed', 'cancelled'],
        'consulting' => ['confirmed', 'completed', 'cancelled'],
        'confirmed' => ['deposit_pending', 'deposit_paid', 'waiting_delivery', 'packaged', 'delivering', 'delivered', 'completed', 'cancelled'],
        'deposit_pending' => ['deposit_paid', 'waiting_delivery', 'packaged', 'delivering', 'delivered', 'completed', 'cancelled'],
        'deposit_paid' => ['waiting_delivery', 'packaged', 'delivering', 'delivered', 'completed', 'cancelled'],
        'waiting_delivery' => ['packaged', 'delivering', 'delivered', 'completed', 'cancelled'],
        'packaged' => ['delivering', 'delivered', 'completed', 'cancelled'],
        'delivering' => ['delivered', 'completed', 'cancelled'],
        'delivered' => ['completed', 'cancelled'],
        'completed' => ['refunded'],
        'cancelled' => [],
        'refunded' => [],
    ];

    private const PACKAGING_TRANSITIONS = [
        'waiting_delivery' => ['packaged', 'cancelled'],
        'packaged' => ['delivering', 'cancelled'],
        'delivering' => ['delivered', 'cancelled'],
        'delivered' => [],
        'cancelled' => [],
    ];

    public function allowedTransitions(User $user, Order $order): array
    {
        if ($user->hasCrmRole('packaging')) {
            return self::PACKAGING_TRANSITIONS[$order->order_status] ?? [];
        }

        $map = $user->hasCrmAnyRole(['admin', 'staff']) ? self::STAFF_TRANSITIONS : self::CTV_TRANSITIONS;

        return $map[$order->order_status] ?? [];
    }

    public function canTransition(User $user, Order $order, string $toStatus): bool
    {
        return in_array($toStatus, $this->allowedTransitions($user, $order), true);
    }

    public function transition(Order $order, string $toStatus, User $user, ?string $note = null): Order
    {
        if (! in_array($toStatus, self::ALL_STATUSES, true)) {
            throw ValidationException::withMessages(['order_status' => 'Trạng thái không hợp lệ.']);
        }

        if ($order->order_status === $toStatus) {
            return $order;
        }

        if (! $this->canTransition($user, $order, $toStatus)) {
            throw ValidationException::withMessages([
                'order_status' => "Không thể chuyển từ {$order->order_status} sang {$toStatus}.",
            ]);
        }

        return DB::transaction(function () use ($order, $toStatus, $user, $note) {
            $fromStatus = $order->order_status;

            $order->update([
                'order_status' => $toStatus,
                'confirmed_at' => $toStatus === 'confirmed' && ! $order->confirmed_at
                    ? now()->toDateString()
                    : $order->confirmed_at,
            ]);

            OrderStatusHistory::create([
                'order_id' => $order->id,
                'from_status' => $fromStatus,
                'to_status' => $toStatus,
                'changed_by' => $user->id,
                'note' => $note ?: "Chuyển trạng thái: {$fromStatus} → {$toStatus}",
            ]);

            $order = $order->fresh();
            $this->notificationService->orderStatusChanged($order, $fromStatus, $toStatus, $user);

            if ($toStatus === 'delivered') {
                $order = $this->orderAutoCompleteService->tryComplete($order, $user);
            }

            return $order;
        });
    }
}
