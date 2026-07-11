<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VoucherOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'order_number',
        'access_token_hash',
        'purchaser_first_name',
        'purchaser_last_name',
        'purchaser_email',
        'purchaser_phone',
        'billing_country_code',
        'currency',
        'subtotal',
        'discount_amount',
        'total_amount',
        'payment_gateway',
        'payment_status',
        'order_status',
        'flywire_checkout_session_id',
        'flywire_payment_id',
        'flywire_payment_reference',
        'flywire_status',
        'flywire_hosted_form_url',
        'paid_at',
        'completed_at',
        'cancelled_at',
        'metadata',
    ];

    protected $casts = [
        'subtotal' => 'integer',
        'discount_amount' => 'integer',
        'total_amount' => 'integer',
        'paid_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(VoucherOrderItem::class);
    }

    public function paymentEvents(): HasMany
    {
        return $this->hasMany(VoucherPaymentEvent::class);
    }

    public function scopePaid(Builder $query): Builder
    {
        return $query->where('payment_status', 'paid');
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->whereIn('payment_status', ['pending', 'payment_session_created', 'payment_initiated', 'processing']);
    }
}
