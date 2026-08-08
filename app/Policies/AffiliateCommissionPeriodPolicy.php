<?php

namespace App\Policies;

use App\Models\AffiliateCommissionPeriod;
use App\Models\Permission;
use App\Models\User;

class AffiliateCommissionPeriodPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo(Permission::AFFILIATE_COMMISSION_VIEW);
    }

    public function view(User $user, AffiliateCommissionPeriod $period): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo(Permission::AFFILIATE_COMMISSION_VALIDATE);
    }

    public function update(User $user, AffiliateCommissionPeriod $period): bool
    {
        return false;
    }

    public function delete(User $user, AffiliateCommissionPeriod $period): bool
    {
        return false;
    }
}
