<?php

namespace App\Policies\Crm;

use App\Models\Crm\Payment;
use App\Models\User;

class PaymentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasCrmAnyRole(['admin', 'staff', 'accounting']);
    }

    public function view(User $user, Payment $payment): bool
    {
        if ($user->hasCrmAnyRole(['admin', 'staff', 'accounting'])) {
            return true;
        }

        $payment->loadMissing('order');

        return $user->hasCrmRole('collaborator')
            && $user->affiliate_id === $payment->order?->affiliate_id;
    }

    public function create(User $user): bool
    {
        return $user->hasCrmAnyRole(['admin', 'staff', 'collaborator', 'packaging'])
            && ! $this->isAccountingOnly($user);
    }

    public function approve(User $user, Payment $payment): bool
    {
        return $user->hasCrmAnyRole(['admin', 'accounting']);
    }

    public function reject(User $user, Payment $payment): bool
    {
        return $user->hasCrmAnyRole(['admin', 'accounting']);
    }

    public function update(User $user, Payment $payment): bool
    {
        return $user->hasCrmAnyRole(['admin', 'staff']);
    }

    public function delete(User $user, Payment $payment): bool
    {
        return $user->hasCrmAnyRole(['admin', 'staff']);
    }

    private function isAccountingOnly(User $user): bool
    {
        return $user->hasCrmRole('accounting')
            && ! $user->hasCrmAnyRole(['admin', 'staff', 'collaborator']);
    }
}
