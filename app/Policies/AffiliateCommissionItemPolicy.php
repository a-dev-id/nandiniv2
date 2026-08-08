<?php

namespace App\Policies;

use App\Models\AffiliateCommissionItem;
use App\Models\Permission;
use App\Models\User;

class AffiliateCommissionItemPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo(Permission::AFFILIATE_COMMISSION_VIEW);
    }

    public function view(User $user, AffiliateCommissionItem $item): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, AffiliateCommissionItem $item): bool
    {
        return false;
    }

    public function delete(User $user, AffiliateCommissionItem $item): bool
    {
        return false;
    }
}
