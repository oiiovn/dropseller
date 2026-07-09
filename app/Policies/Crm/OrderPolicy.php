<?php

namespace App\Policies\Crm;

use App\Models\Crm\Order;
use App\Models\User;
use App\Services\Crm\OrderDeletionService;

class OrderPolicy
{
    public const FULFILLMENT_STATUSES = [
        'waiting_delivery',
        'packaged',
        'delivering',
        'delivered',
    ];

    public function viewAny(User $user): bool
    {
        return $user->hasCrmAnyRole(['admin', 'staff', 'collaborator', 'packaging', 'accounting']);
    }

    public function view(User $user, Order $order): bool
    {
        if ($user->hasCrmRole('packaging')) {
            return in_array($order->order_status, self::FULFILLMENT_STATUSES, true);
        }

        return $user->hasCrmAnyRole(['admin', 'staff', 'accounting'])
            || ($user->hasCrmRole('collaborator') && $user->affiliate_id === $order->affiliate_id);
    }

    public function create(User $user): bool
    {
        return $user->hasCrmAnyRole(['admin', 'staff', 'collaborator']);
    }

    public function update(User $user, Order $order): bool
    {
        if ($user->hasCrmRole('packaging')) {
            return in_array($order->order_status, ['waiting_delivery', 'packaged', 'delivering'], true);
        }

        return $user->hasCrmAnyRole(['admin', 'staff'])
            || ($user->hasCrmRole('collaborator') && $user->affiliate_id === $order->affiliate_id);
    }

    public function delete(User $user, Order $order): bool
    {
        if ($user->hasCrmAnyRole(['admin', 'staff'])) {
            return true;
        }

        if ($user->hasCrmRole('collaborator') && $user->affiliate_id === $order->affiliate_id) {
            return app(OrderDeletionService::class)->canCollaboratorDelete($order);
        }

        return false;
    }
}
