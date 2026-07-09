<?php

namespace App\Services\Crm;

use App\Models\Crm\Debt;
use App\Models\Crm\Order;
use App\Models\Crm\Payment;
use App\Models\Crm\UserNotification;
use App\Models\User;
use Illuminate\Support\Collection;

class CrmNotificationService
{
    public const ORDER_STATUS_LABELS = [
        'new' => 'Mới',
        'consulting' => 'Đang tư vấn',
        'confirmed' => 'Đã chốt',
        'deposit_pending' => 'Chờ cọc',
        'deposit_paid' => 'Đã cọc',
        'waiting_delivery' => 'Chờ đóng gói',
        'packaged' => 'Đã đóng gói',
        'delivering' => 'Đang giao',
        'delivered' => 'Đã giao',
        'completed' => 'Hoàn thành',
        'cancelled' => 'Đã hủy',
        'refunded' => 'Hoàn tiền',
    ];

    private const STATUS_ROLE_TARGETS = [
        'waiting_delivery' => ['packaging', 'staff'],
        'packaged' => ['packaging', 'staff'],
        'delivering' => ['packaging', 'staff'],
        'delivered' => ['staff'],
        'deposit_pending' => ['staff'],
        'deposit_paid' => ['staff', 'packaging'],
        'completed' => ['staff'],
        'cancelled' => ['staff'],
    ];

    public function listForUser(User $user, int $limit = 15, bool $unreadOnly = false): Collection
    {
        $query = UserNotification::query()
            ->where('user_id', $user->id)
            ->latest();

        if ($unreadOnly) {
            $query->whereNull('read_at');
        }

        return $query->limit(max(1, min($limit, 50)))->get();
    }

    public function unreadCount(User $user): int
    {
        return UserNotification::query()
            ->where('user_id', $user->id)
            ->whereNull('read_at')
            ->count();
    }

    public function markRead(User $user, UserNotification $notification): UserNotification
    {
        abort_if($notification->user_id !== $user->id, 403);

        if (! $notification->read_at) {
            $notification->update(['read_at' => now()]);
        }

        return $notification->fresh();
    }

    public function markAllRead(User $user): int
    {
        return UserNotification::query()
            ->where('user_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    public function paymentSubmitted(Payment $payment, User $actor): void
    {
        $payment->loadMissing(['order:id,order_code', 'recorder:id,name']);
        $orderCode = $payment->order?->order_code ?? ('#' . $payment->order_id);
        $amount = number_format((float) $payment->amount_jpy, 0, '.', ',');

        $this->notifyRoles(
            ['accounting', 'admin'],
            [
                'type' => 'payment_pending',
                'category' => 'warning',
                'title' => 'Thanh toán chờ duyệt',
                'message' => "Đơn {$orderCode}: {$amount}円 cần kế toán xác nhận (gửi bởi {$actor->name}).",
                'action_url' => '/crm/payments?approval_status=pending',
                'related_type' => Payment::class,
                'related_id' => $payment->id,
            ],
            $actor->id
        );
    }

    public function paymentApproved(Payment $payment, User $actor): void
    {
        $payment->loadMissing(['order:id,order_code', 'recorder:id,name']);
        $orderCode = $payment->order?->order_code ?? ('#' . $payment->order_id);
        $amount = number_format((float) $payment->amount_jpy, 0, '.', ',');

        if ($payment->recorded_by && $payment->recorded_by !== $actor->id) {
            $this->notifyUser($payment->recorded_by, [
                'type' => 'payment_approved',
                'category' => 'success',
                'title' => 'Kế toán đã duyệt thanh toán',
                'message' => "Thanh toán {$amount}円 cho đơn {$orderCode} đã được xác nhận.",
                'action_url' => "/crm/orders/{$payment->order_id}",
                'related_type' => Payment::class,
                'related_id' => $payment->id,
            ]);
        }
    }

    public function paymentRejected(Payment $payment, User $actor, string $reason): void
    {
        $payment->loadMissing(['order:id,order_code']);
        $orderCode = $payment->order?->order_code ?? ('#' . $payment->order_id);

        if ($payment->recorded_by && $payment->recorded_by !== $actor->id) {
            $this->notifyUser($payment->recorded_by, [
                'type' => 'payment_rejected',
                'category' => 'danger',
                'title' => 'Kế toán từ chối thanh toán',
                'message' => "Thanh toán đơn {$orderCode} bị từ chối: {$reason}",
                'action_url' => "/crm/orders/{$payment->order_id}",
                'related_type' => Payment::class,
                'related_id' => $payment->id,
            ]);
        }
    }

    public function orderCreated(Order $order, User $actor): void
    {
        $order->loadMissing('customer:id,full_name');
        $customerName = $order->customer?->full_name ?? 'Khách hàng';

        $this->notifyRoles(
            ['staff', 'admin'],
            [
                'type' => 'order_created',
                'category' => 'info',
                'title' => 'Đơn hàng mới',
                'message' => "Đơn {$order->order_code} — {$customerName} vừa được tạo bởi {$actor->name}.",
                'action_url' => "/crm/orders/{$order->id}",
                'related_type' => Order::class,
                'related_id' => $order->id,
            ],
            $actor->id
        );
    }

    public function orderStatusChanged(Order $order, string $fromStatus, string $toStatus, User $actor): void
    {
        $fromLabel = self::ORDER_STATUS_LABELS[$fromStatus] ?? $fromStatus;
        $toLabel = self::ORDER_STATUS_LABELS[$toStatus] ?? $toStatus;

        $payload = [
            'type' => 'order_status',
            'category' => in_array($toStatus, ['cancelled', 'refunded'], true) ? 'danger' : 'info',
            'title' => 'Cập nhật trạng thái đơn',
            'message' => "Đơn {$order->order_code}: {$fromLabel} → {$toLabel} (bởi {$actor->name}).",
            'action_url' => "/crm/orders/{$order->id}",
            'related_type' => Order::class,
            'related_id' => $order->id,
        ];

        $roles = self::STATUS_ROLE_TARGETS[$toStatus] ?? [];
        if ($roles !== []) {
            $this->notifyRoles($roles, $payload, $actor->id);
        }

        $this->notifyAffiliateUsers($order->affiliate_id, $payload, $actor->id);
    }

    public function debtOverdue(Debt $debt): void
    {
        $debt->loadMissing(['order:id,order_code,affiliate_id', 'customer:id,full_name']);
        $orderCode = $debt->order?->order_code ?? ('#' . $debt->order_id);
        $amount = number_format((float) $debt->outstanding_jpy, 0, '.', ',');

        $payload = [
            'type' => 'debt_overdue',
            'category' => 'danger',
            'title' => 'Công nợ quá hạn',
            'message' => "Đơn {$orderCode} còn nợ {$amount}円 đã quá hạn thanh toán.",
            'action_url' => "/crm/orders/{$debt->order_id}",
            'related_type' => Debt::class,
            'related_id' => $debt->id,
        ];

        $this->notifyRoles(['staff', 'accounting', 'admin'], $payload);

        if ($debt->order?->affiliate_id) {
            $this->notifyAffiliateUsers($debt->order->affiliate_id, $payload);
        }
    }

    private function notifyUser(int $userId, array $payload): void
    {
        $this->createNotification($userId, $payload);
    }

    private function notifyRoles(array $roles, array $payload, ?int $exceptUserId = null): void
    {
        $userIds = User::query()
            ->whereHas('crmRoles', fn ($query) => $query->whereIn('name', $roles))
            ->when($exceptUserId, fn ($query) => $query->where('id', '!=', $exceptUserId))
            ->pluck('id');

        foreach ($userIds as $userId) {
            $this->createNotification((int) $userId, $payload);
        }
    }

    private function notifyAffiliateUsers(?int $affiliateId, array $payload, ?int $exceptUserId = null): void
    {
        if (! $affiliateId) {
            return;
        }

        $userIds = User::query()
            ->where('affiliate_id', $affiliateId)
            ->whereHas('crmRoles', fn ($query) => $query->where('name', 'collaborator'))
            ->when($exceptUserId, fn ($query) => $query->where('id', '!=', $exceptUserId))
            ->pluck('id');

        foreach ($userIds as $userId) {
            $this->createNotification((int) $userId, $payload);
        }
    }

    private function createNotification(int $userId, array $payload): UserNotification
    {
        if ($this->shouldSkipDuplicate($userId, $payload)) {
            return new UserNotification($payload);
        }

        return UserNotification::create([
            'user_id' => $userId,
            'type' => $payload['type'],
            'category' => $payload['category'] ?? 'info',
            'title' => $payload['title'],
            'message' => $payload['message'],
            'action_url' => $payload['action_url'] ?? null,
            'related_type' => $payload['related_type'] ?? null,
            'related_id' => $payload['related_id'] ?? null,
        ]);
    }

    private function shouldSkipDuplicate(int $userId, array $payload): bool
    {
        if (($payload['type'] ?? '') !== 'debt_overdue') {
            return false;
        }

        return UserNotification::query()
            ->where('user_id', $userId)
            ->where('type', 'debt_overdue')
            ->where('related_type', $payload['related_type'] ?? null)
            ->where('related_id', $payload['related_id'] ?? null)
            ->where('created_at', '>=', now()->subDay())
            ->exists();
    }
}
