<?php

namespace App\Policies;

use App\Models\Affiliate;
use App\Models\AffiliateMarketingAsset;
use App\Models\Permission;
use App\Models\User;

class AffiliateMarketingAssetPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo(Permission::AFFILIATE_MARKETING_ASSET_MANAGE);
    }

    public function view(User|Affiliate $user, AffiliateMarketingAsset $asset): bool
    {
        if ($user instanceof Affiliate) {
            return $user->isApproved()
                && $user->hasPermissionTo(Permission::AFFILIATE_MARKETING_ASSET_VIEW_OWN)
                && $asset->isAvailable();
        }

        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function update(User $user, AffiliateMarketingAsset $asset): bool
    {
        return $this->viewAny($user);
    }

    public function delete(User $user, AffiliateMarketingAsset $asset): bool
    {
        return $this->viewAny($user);
    }
}
