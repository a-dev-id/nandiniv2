<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Member extends Authenticatable
{
    use Notifiable;

    public const SOURCE_AUTO_JOIN = 'auto_join';
    public const SOURCE_MANUAL_REGISTER = 'manual_register';

    public const TIER_BRONZE = 'bronze';
    public const TIER_SILVER = 'silver';
    public const TIER_GOLD = 'gold';
    public const TIER_PLATINUM = 'platinum';

    protected $fillable = [
        'first_name',
        'last_name',
        'name',
        'email',
        'phone_number',
        'country',
        'address',
        'password',
        'must_change_password',
        'member_source',
        'tier',
        'membership_started_at',
        'membership_expires_at',
        'last_tier_downgraded_at',
        'marketing_consent',
        'points',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'membership_started_at' => 'datetime',
        'membership_expires_at' => 'datetime',
        'last_tier_downgraded_at' => 'datetime',
        'marketing_consent' => 'boolean',
        'must_change_password' => 'boolean',
        'points' => 'integer',
        'password' => 'hashed',
    ];

    public function getFullNameAttribute(): string
    {
        $fullName = trim(($this->first_name ?? '') . ' ' . ($this->last_name ?? ''));

        return $fullName !== '' ? $fullName : ($this->name ?? '');
    }

    public function getTierLabelAttribute(): string
    {
        return match ($this->tier) {
            self::TIER_PLATINUM => 'Jnana / Platinum',
            self::TIER_GOLD => 'Dhyana / Gold',
            self::TIER_SILVER => 'Upaya / Silver',
            default => 'Dana / Bronze',
        };
    }

    public static function getTierByPoints(int $points): string
    {
        return match (true) {
            $points >= 1201 => self::TIER_PLATINUM,
            $points >= 801 => self::TIER_GOLD,
            $points >= 401 => self::TIER_SILVER,
            default => self::TIER_BRONZE,
        };
    }

    public static function getMinimumPointsForTier(string $tier): int
    {
        return match ($tier) {
            self::TIER_PLATINUM => 1201,
            self::TIER_GOLD => 801,
            self::TIER_SILVER => 401,
            default => 0,
        };
    }

    public static function getDowngradedTier(string $tier): string
    {
        return match ($tier) {
            self::TIER_PLATINUM => self::TIER_GOLD,
            self::TIER_GOLD => self::TIER_SILVER,
            self::TIER_SILVER => self::TIER_BRONZE,
            default => self::TIER_BRONZE,
        };
    }

    public function applyYearlyTierDowngrade(): bool
    {
        if (! $this->membership_expires_at) {
            return false;
        }

        if ($this->membership_expires_at->isFuture()) {
            return false;
        }

        $newTier = self::getDowngradedTier($this->tier ?? self::TIER_BRONZE);
        $newPoints = self::getMinimumPointsForTier($newTier);

        $this->forceFill([
            'tier' => $newTier,
            'points' => $newPoints,
            'membership_started_at' => now(),
            'membership_expires_at' => now()->addYear(),
            'last_tier_downgraded_at' => now(),
        ])->save();

        return true;
    }

    public function syncTierFromPoints(): void
    {
        $this->tier = self::getTierByPoints((int) $this->points);
    }
}
