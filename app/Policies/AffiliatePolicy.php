<?php

namespace App\Policies;

use App\Models\Affiliate;
use App\Models\Permission;
use App\Models\User;

class AffiliatePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo(Permission::AFFILIATE_VIEW);
    }

    public function view(User|Affiliate $user, Affiliate $affiliate): bool
    {
        if ($user instanceof Affiliate) {
            return $user->is($affiliate)
                && $user->hasPermissionTo(Permission::AFFILIATE_DASHBOARD_VIEW_OWN);
        }

        return $user->hasPermissionTo(Permission::AFFILIATE_VIEW);
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo(Permission::AFFILIATE_CREATE);
    }

    public function update(User $user, Affiliate $affiliate): bool
    {
        return $affiliate->isPending() && $user->hasPermissionTo(Permission::AFFILIATE_UPDATE);
    }

    public function delete(User $user, Affiliate $affiliate): bool
    {
        return false;
    }

    public function approve(User $user, Affiliate $affiliate): bool
    {
        return $affiliate->isPending() && $user->hasPermissionTo(Permission::AFFILIATE_APPROVE);
    }

    public function reject(User $user, Affiliate $affiliate): bool
    {
        return $affiliate->isPending() && $user->hasPermissionTo(Permission::AFFILIATE_REJECT);
    }
}
