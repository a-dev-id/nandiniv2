<?php

namespace App\Policies;

use App\Models\AffiliateProgramSetting;
use App\Models\Permission;
use App\Models\User;

class AffiliateProgramSettingPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo(Permission::AFFILIATE_SETTING_MANAGE);
    }

    public function view(User $user, AffiliateProgramSetting $setting): bool
    {
        return $this->viewAny($user);
    }

    public function update(User $user, AffiliateProgramSetting $setting): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function delete(User $user, AffiliateProgramSetting $setting): bool
    {
        return false;
    }
}
