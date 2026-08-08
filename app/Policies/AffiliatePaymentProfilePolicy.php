<?php

namespace App\Policies;

use App\Models\AffiliatePaymentProfile;
use App\Models\Permission;
use App\Models\User;

class AffiliatePaymentProfilePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo(Permission::AFFILIATE_PAYMENT_PROFILE_VIEW);
    }

    public function view(User $user, AffiliatePaymentProfile $profile): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, AffiliatePaymentProfile $profile): bool
    {
        return $user->hasPermissionTo(Permission::AFFILIATE_PAYMENT_PROFILE_MANAGE);
    }

    public function delete(User $user, AffiliatePaymentProfile $profile): bool
    {
        return false;
    }
}
