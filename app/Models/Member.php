<?php

namespace App\Models;

use App\Services\MembershipEmailRelayService;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
        'booking_check_in',
        'booking_check_out',
        'password',
        'must_change_password',
        'member_source',
        'tier',
        'membership_started_at',
        'membership_expires_at',
        'last_tier_downgraded_at',
        'membership_expiry_reminder_sent_at',
        'checkout_notification_sent_at',
        'last_login_at',
        'marketing_consent',
        'points',
        'email_verified_at',
        'welcome_email_sent_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'welcome_email_sent_at' => 'datetime',
        'date_of_birth' => 'date',
        'booking_check_in' => 'date',
        'booking_check_out' => 'date',
        'membership_started_at' => 'datetime',
        'membership_expires_at' => 'datetime',
        'last_tier_downgraded_at' => 'datetime',
        'membership_expiry_reminder_sent_at' => 'datetime',
        'checkout_notification_sent_at' => 'datetime',
        'last_login_at' => 'datetime',
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

    public function voucherOrders(): HasMany
    {
        return $this->hasMany(VoucherOrder::class);
    }

    public function issuedVouchers(): HasMany
    {
        return $this->hasMany(IssuedVoucher::class);
    }

    public function syncedBookings(): HasMany
    {
        return $this->hasMany(SyncedWebhotelierBooking::class);
    }

    public function sendPasswordResetNotification($token): void
    {
        $resetUrl = route('membership.password.reset', [
            'token' => $token,
            'email' => $this->getEmailForPasswordReset(),
        ]);

        // Password reset must not depend on this host's SMTP; relay the rendered template instead.
        $result = app(MembershipEmailRelayService::class)->sendView('emails.membership.reset-password', [
            'member' => $this,
            'resetUrl' => $resetUrl,
            'expiresInMinutes' => config('auth.passwords.members.expire', 60),
        ], [
            'to' => $this->getEmailForPasswordReset(),
            'bcc' => $this->guestBcc(),
            'subject' => 'Reset Your Nandini Inner Circle Password',
        ]);

        if (! $result['success']) {
            Log::warning('Member password reset email could not be sent through relay.', [
                'member_id' => $this->id,
                'email' => $this->email,
                'relay_response' => $result,
            ]);
        }
    }

    public function getFullNameAttribute(): string
    {
        $fullName = trim(($this->first_name ?? '') . ' ' . ($this->last_name ?? ''));

        return $fullName !== '' ? $fullName : ($this->name ?? '');
    }

    public function getTierLabelAttribute(): string
    {
        return self::getTierLabelForTier((string) $this->tier);
    }

    public static function getTierLabelForTier(string $tier): string
    {
        return match ($tier) {
            self::TIER_PLATINUM => 'Jnana',
            self::TIER_GOLD => 'Dhyana',
            self::TIER_SILVER => 'Upaya',
            default => 'Dana',
        };
    }

    public static function getTierRank(string $tier): int
    {
        return match ($tier) {
            self::TIER_PLATINUM => 4,
            self::TIER_GOLD => 3,
            self::TIER_SILVER => 2,
            default => 1,
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

    public static function getMaximumPointsForTier(string $tier): ?int
    {
        return match ($tier) {
            self::TIER_BRONZE => 400,
            self::TIER_SILVER => 800,
            self::TIER_GOLD => 1200,
            default => null,
        };
    }

    public static function calculatePointsFromConsumption(int|float|null $totalConsumption): int
    {
        $totalConsumption = (float) $totalConsumption;

        if ($totalConsumption <= 0) {
            return 0;
        }

        return (int) floor((($totalConsumption / 1.21) * 0.05) / 1000);
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
        return app(\App\Services\MembershipLifecycleService::class)->processExpiredMember($this) === 'downgraded';
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

            $previousTier = (string) ($this->tier ?? self::TIER_BRONZE);
            $previousPoints = (int) $this->points;

            $transaction = $this->pointTransactions()->create([
                'type' => $type,
                'points' => $points,
                'description' => $description,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
            ]);

            $this->points = (int) $this->points + $points;

            if ($type !== self::POINT_TYPE_REDEEM) {
                $this->syncTierFromPoints();
            }

            if ($type !== self::POINT_TYPE_REDEEM && $points > 0) {
                $this->extendMembershipValidityForPointAddition();
            }

            $this->save();

            $newTier = (string) ($this->tier ?? self::TIER_BRONZE);

            if ($type === self::POINT_TYPE_EARN) {
                DB::afterCommit(function () use ($points, $description, $previousTier, $previousPoints, $newTier): void {
                    $result = app(MembershipEmailRelayService::class)->sendView('emails.membership.points-added', [
                        'member' => $this,
                        'pointsAdded' => $points,
                        'totalPoints' => (int) $this->points,
                        'previousPoints' => $previousPoints,
                        'previousTierLabel' => self::getTierLabelForTier($previousTier),
                        'newTierLabel' => self::getTierLabelForTier($newTier),
                        'description' => $description,
                        'dashboardUrl' => route('membership.dashboard'),
                    ], [
                        'to' => $this->email,
                        'bcc' => $this->guestBcc(),
                        'subject' => 'Your Points Have Been Added to Your Account',
                    ]);

                    if (! $result['success']) {
                        Log::warning('Member points added email could not be sent through relay.', [
                            'member_id' => $this->id,
                            'email' => $this->email,
                            'points_added' => $points,
                            'relay_response' => $result,
                        ]);
                    }
                });
            }

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

    protected function extendMembershipValidityForPointAddition(): void
    {
        $now = now();
        $currentExpiry = $this->membership_expires_at;

        if (! $this->membership_started_at || ($currentExpiry && $currentExpiry->lte($now))) {
            $this->membership_started_at = $now;
        }

        $this->membership_expires_at = $currentExpiry && $currentExpiry->gt($now)
            ? $currentExpiry->copy()->addYear()
            : $now->copy()->addYear();

        $this->membership_expiry_reminder_sent_at = null;
    }

    /**
     * @return array<int, string>
     */
    protected function guestBcc(): array
    {
        $bcc = trim((string) config('mail.guest_bcc'));

        return $bcc === '' ? [] : [$bcc];
    }
}
