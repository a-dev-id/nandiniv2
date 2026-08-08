<?php

namespace App\Models;

use App\Enums\AffiliatePayoutStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AffiliatePayout extends Model
{
    protected $fillable = [
        'payout_number',
        'affiliate_id',
        'currency',
        'gross_commission_amount',
        'adjustment_amount',
        'adjustment_reason',
        'net_payout_amount',
        'payment_method_snapshot',
        'payment_details_masked_snapshot',
        'status',
        'due_at',
        'prepared_at',
        'prepared_by',
        'processing_at',
        'processing_by',
        'paid_at',
        'paid_by',
        'payment_reference',
        'failure_reason',
        'cancelled_at',
        'cancelled_by',
        'cancellation_reason',
    ];

    protected $casts = [
        'gross_commission_amount' => 'decimal:2',
        'adjustment_amount' => 'decimal:2',
        'net_payout_amount' => 'decimal:2',
        'status' => AffiliatePayoutStatus::class,
        'due_at' => 'datetime',
        'prepared_at' => 'datetime',
        'processing_at' => 'datetime',
        'paid_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function affiliate(): BelongsTo
    {
        return $this->belongsTo(Affiliate::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(AffiliatePayoutItem::class);
    }

    public function preparer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'prepared_by');
    }

    public function processingUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processing_by');
    }

    public function paidUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'paid_by');
    }
}
