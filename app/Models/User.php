<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Concerns\HasRolesAndPermissions;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRolesAndPermissions, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $panel->getId() === 'admin'
            && ($this->hasRole(Role::ADMINISTRATOR)
                || $this->hasPermissionTo(Permission::AFFILIATE_VIEW)
                || $this->hasPermissionTo(Permission::AFFILIATE_BOOKING_VIEW)
                || $this->hasPermissionTo(Permission::AFFILIATE_COMMISSION_VIEW)
                || $this->hasPermissionTo(Permission::AFFILIATE_PAYOUT_VIEW)
                || $this->hasPermissionTo(Permission::AFFILIATE_PAYMENT_PROFILE_VIEW)
                || $this->hasPermissionTo(Permission::AFFILIATE_MARKETING_ASSET_MANAGE)
                || $this->hasPermissionTo(Permission::AFFILIATE_REPORT_VIEW)
                || $this->hasPermissionTo(Permission::AFFILIATE_SYSTEM_HEALTH_VIEW));
    }
}
