<?php

namespace App\Policies\Crm;

use App\Models\Crm\Commission;
use App\Models\User;
class CommissionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasCrmAnyRole(['admin', 'staff', 'collaborator', 'accounting']);
    }

    public function view(User $user, Commission $commission): bool
    {
        return $user->hasCrmAnyRole(['admin', 'staff', 'accounting'])
            || ($user->hasCrmRole('collaborator') && $user->affiliate_id === $commission->affiliate_id);
    }

    public function create(User $user): bool
    {
        return $user->hasCrmAnyRole(['admin', 'staff']);
    }

    public function update(User $user, Commission $commission): bool
    {
        return $user->hasCrmAnyRole(['admin', 'staff']);
    }

    public function delete(User $user, Commission $commission): bool
    {
        return $user->hasCrmRole('admin');
    }
}
