<?php

namespace App\Policies;

use App\Models\AffiliatePayoutMinimum;
use App\Models\Permission;
use App\Models\User;

class AffiliatePayoutMinimumPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo(Permission::AFFILIATE_SETTING_MANAGE);
    }

    public function view(User $user, AffiliatePayoutMinimum $minimum): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function update(User $user, AffiliatePayoutMinimum $minimum): bool
    {
        return $this->viewAny($user);
    }

    public function delete(User $user, AffiliatePayoutMinimum $minimum): bool
    {
        return $this->viewAny($user);
    }
}
