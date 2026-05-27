<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class MemberRewardRedemption extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_USED = 'used';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_EXPIRED = 'expired';

    protected $fillable = [
        'member_id',
        'reward_id',
        'member_point_transaction_id',
        'reward_name',
        'points_used',
        'status',
        'redemption_code',
        'used_at',
        'cancelled_at',
        'expires_at',
        'notes',
    ];

    protected $casts = [
        'points_used' => 'integer',
        'used_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (MemberRewardRedemption $redemption) {
            if (! $redemption->redemption_code) {
                $redemption->redemption_code = self::generateRedemptionCode();
            }
        });
    }

    public static function generateRedemptionCode(): string
    {
        do {
            $code = 'RDM-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));
        } while (self::where('redemption_code', $code)->exists());

        return $code;
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function reward(): BelongsTo
    {
        return $this->belongsTo(Reward::class);
    }

    public function pointTransaction(): BelongsTo
    {
        return $this->belongsTo(MemberPointTransaction::class, 'member_point_transaction_id');
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isUsed(): bool
    {
        return $this->status === self::STATUS_USED;
    }

    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    public function isExpired(): bool
    {
        if ($this->status === self::STATUS_EXPIRED) {
            return true;
        }

        return $this->expires_at && $this->expires_at->isPast();
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_USED => 'Used',
            self::STATUS_CANCELLED => 'Cancelled',
            self::STATUS_EXPIRED => 'Expired',
            default => 'Pending',
        };
    }

    public function markAsUsed(?string $notes = null): void
    {
        $this->forceFill([
            'status' => self::STATUS_USED,
            'used_at' => now(),
            'notes' => $notes ?? $this->notes,
        ])->save();
    }

    public function markAsCancelled(?string $notes = null): void
    {
        $this->forceFill([
            'status' => self::STATUS_CANCELLED,
            'cancelled_at' => now(),
            'notes' => $notes ?? $this->notes,
        ])->save();
    }

    public function markAsExpired(): void
    {
        $this->forceFill([
            'status' => self::STATUS_EXPIRED,
        ])->save();
    }
}
