<?php

namespace App\Policies;

use App\Models\AffiliatePayout;
use App\Models\Permission;
use App\Models\User;

class AffiliatePayoutPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo(Permission::AFFILIATE_PAYOUT_VIEW);
    }

    public function view(User $user, AffiliatePayout $payout): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo(Permission::AFFILIATE_PAYOUT_MANAGE);
    }

    public function update(User $user, AffiliatePayout $payout): bool
    {
        return false;
    }

    public function delete(User $user, AffiliatePayout $payout): bool
    {
        return false;
    }
}
