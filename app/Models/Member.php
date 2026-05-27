<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class Member extends Authenticatable
{
    use Notifiable;

    public const SOURCE_AUTO_JOIN = 'auto_join';
    public const SOURCE_MANUAL_REGISTER = 'manual_register';

    public const TIER_BRONZE = 'bronze';
    public const TIER_SILVER = 'silver';
    public const TIER_GOLD = 'gold';
    public const TIER_PLATINUM = 'platinum';

    public const POINT_TYPE_EARN = 'earn';
    public const POINT_TYPE_REDEEM = 'redeem';
    public const POINT_TYPE_ADJUSTMENT = 'adjustment';

    protected $fillable = [
        'first_name',
        'last_name',
        'name',
        'email',
        'phone_number',
        'date_of_birth',
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
        'date_of_birth' => 'date',
        'membership_started_at' => 'datetime',
        'membership_expires_at' => 'datetime',
        'last_tier_downgraded_at' => 'datetime',
        'marketing_consent' => 'boolean',
        'must_change_password' => 'boolean',
        'points' => 'integer',
        'password' => 'hashed',
    ];

    public function pointTransactions(): HasMany
    {
        return $this->hasMany(MemberPointTransaction::class);
    }

    public function rewardRedemptions(): HasMany
    {
        return $this->hasMany(MemberRewardRedemption::class);
    }

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

    public function getCalculatedPointsAttribute(): int
    {
        return (int) $this->pointTransactions()->sum('points');
    }

    public function getAvailablePointsAttribute(): int
    {
        return (int) $this->points;
    }

    public function canRedeemPoints(int $points): bool
    {
        return $points > 0 && $this->available_points >= $points;
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

        $this->pointTransactions()->create([
            'type' => self::POINT_TYPE_ADJUSTMENT,
            'points' => $newPoints,
            'description' => 'Yearly tier downgrade point adjustment',
            'reference_type' => 'member',
            'reference_id' => $this->id,
        ]);

        return true;
    }

    public function syncTierFromPoints(): void
    {
        $this->tier = self::getTierByPoints((int) $this->points);
    }

    public function refreshPointsFromTransactions(): void
    {
        $this->points = $this->calculated_points;
        $this->syncTierFromPoints();
        $this->save();
    }

    public function addPointTransaction(
        string $type,
        int $points,
        ?string $description = null,
        ?string $referenceType = null,
        ?int $referenceId = null
    ): MemberPointTransaction {
        if (! in_array($type, [
            self::POINT_TYPE_EARN,
            self::POINT_TYPE_REDEEM,
            self::POINT_TYPE_ADJUSTMENT,
        ], true)) {
            throw new InvalidArgumentException('Invalid point transaction type.');
        }

        if ($points === 0) {
            throw new InvalidArgumentException('Point amount cannot be zero.');
        }

        if ($type === self::POINT_TYPE_EARN && $points < 0) {
            $points = abs($points);
        }

        if ($type === self::POINT_TYPE_REDEEM && $points > 0) {
            $points = $points * -1;
        }

        return DB::transaction(function () use ($type, $points, $description, $referenceType, $referenceId) {
            if ($type === self::POINT_TYPE_REDEEM && ((int) $this->points + $points) < 0) {
                throw new InvalidArgumentException('Member does not have enough points.');
            }

            $transaction = $this->pointTransactions()->create([
                'type' => $type,
                'points' => $points,
                'description' => $description,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
            ]);

            $this->points = (int) $this->points + $points;
            $this->syncTierFromPoints();
            $this->save();

            return $transaction;
        });
    }

    public function earnPoints(
        int $points,
        ?string $description = null,
        ?string $referenceType = null,
        ?int $referenceId = null
    ): MemberPointTransaction {
        return $this->addPointTransaction(
            self::POINT_TYPE_EARN,
            abs($points),
            $description,
            $referenceType,
            $referenceId
        );
    }

    public function redeemPoints(
        int $points,
        ?string $description = null,
        ?string $referenceType = null,
        ?int $referenceId = null
    ): MemberPointTransaction {
        return $this->addPointTransaction(
            self::POINT_TYPE_REDEEM,
            abs($points),
            $description,
            $referenceType,
            $referenceId
        );
    }
}
