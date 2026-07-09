<?php

namespace App\Policies\Crm;

use App\Models\Crm\Affiliate;
use App\Models\User;
class AffiliatePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasCrmAnyRole(['admin', 'staff']);
    }

    public function view(User $user, Affiliate $affiliate): bool
    {
        return $user->hasCrmAnyRole(['admin', 'staff'])
            || ($user->hasCrmRole('collaborator') && $user->affiliate_id === $affiliate->id);
    }

    public function create(User $user): bool
    {
        return $user->hasCrmRole('admin');
    }

    public function update(User $user, Affiliate $affiliate): bool
    {
        return $user->hasCrmRole('admin');
    }

    public function delete(User $user, Affiliate $affiliate): bool
    {
        return $user->hasCrmRole('admin');
    }
}
