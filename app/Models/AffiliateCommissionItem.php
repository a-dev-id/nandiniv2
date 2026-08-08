<?php

namespace App\Models;

use App\Enums\AffiliateCommissionItemStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AffiliateCommissionItem extends Model
{
    protected $fillable = [
        'commission_period_id',
        'affiliate_booking_id',
        'affiliate_id',
        'currency',
        'room_revenue_snapshot',
        'commission_rate_snapshot',
        'original_commission_amount',
        'approved_commission_amount',
        'status',
        'hold_reason',
        'exclusion_reason',
        'adjustment_reason',
        'source_changed_after_review',
        'discrepancy_warning',
        'reviewed_at',
        'reviewed_by',
        'approved_at',
        'approved_by',
    ];

    protected $casts = [
        'room_revenue_snapshot' => 'decimal:2',
        'commission_rate_snapshot' => 'decimal:2',
        'original_commission_amount' => 'decimal:2',
        'approved_commission_amount' => 'decimal:2',
        'status' => AffiliateCommissionItemStatus::class,
        'source_changed_after_review' => 'boolean',
        'reviewed_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    public function period(): BelongsTo
    {
        return $this->belongsTo(AffiliateCommissionPeriod::class, 'commission_period_id');
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(AffiliateBooking::class, 'affiliate_booking_id');
    }

    public function affiliate(): BelongsTo
    {
        return $this->belongsTo(Affiliate::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function payoutItem(): HasOne
    {
        return $this->hasOne(AffiliatePayoutItem::class, 'affiliate_commission_item_id');
    }

    public function payableAmount(): string
    {
        return (string) ($this->approved_commission_amount ?? $this->original_commission_amount);
    }
}
