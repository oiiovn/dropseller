<?php

namespace App\Policies\Crm;

use App\Models\Crm\Customer;
use App\Models\User;
class CustomerPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasCrmAnyRole(['admin', 'staff', 'collaborator']);
    }

    public function view(User $user, Customer $customer): bool
    {
        return $user->hasCrmAnyRole(['admin', 'staff'])
            || ($user->hasCrmRole('collaborator') && $user->affiliate_id === $customer->affiliate_id);
    }

    public function create(User $user): bool
    {
        return $user->hasCrmAnyRole(['admin', 'staff', 'collaborator']);
    }

    public function update(User $user, Customer $customer): bool
    {
        return $user->hasCrmAnyRole(['admin', 'staff'])
            || ($user->hasCrmRole('collaborator') && $user->affiliate_id === $customer->affiliate_id);
    }

    public function delete(User $user, Customer $customer): bool
    {
        return $user->hasCrmAnyRole(['admin', 'staff']);
    }
}
