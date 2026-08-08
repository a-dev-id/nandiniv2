<?php

namespace App\Models\Concerns;

use App\Models\Role;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait HasRolesAndPermissions
{
    public function roles(): MorphToMany
    {
        return $this->morphToMany(Role::class, 'model', 'model_has_roles');
    }

    public function assignRole(Role|string $role): static
    {
        $role = $role instanceof Role
            ? $role
            : Role::query()->where('slug', $role)->firstOrFail();

        $this->roles()->syncWithoutDetaching([$role->getKey()]);
        $this->unsetRelation('roles');

        return $this;
    }

    public function hasRole(string $role): bool
    {
        return $this->roles()->where('slug', $role)->exists();
    }

    public function hasPermissionTo(string $permission): bool
    {
        if ($this->hasRole(Role::ADMINISTRATOR)) {
            return true;
        }

        return $this->roles()
            ->whereHas('permissions', fn ($query) => $query->where('name', $permission))
            ->exists();
    }
}
