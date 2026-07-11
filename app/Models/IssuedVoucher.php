<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IssuedVoucher extends Model
{
    use HasFactory;

    protected $fillable = [
        'voucher_order_item_id',
        'voucher_id',
        'member_id',
        'voucher_code',
        'verification_token_hash',
        'recipient_name',
        'recipient_email',
        'title',
        'description_snapshot',
        'terms_snapshot',
        'original_value',
        'remaining_value',
        'currency',
        'issued_at',
        'valid_from',
        'expires_at',
        'status',
        'pdf_path',
        'delivered_at',
        'redeemed_at',
        'cancelled_at',
        'metadata',
    ];

    protected $casts = [
        'original_value' => 'integer',
        'remaining_value' => 'integer',
        'issued_at' => 'datetime',
        'valid_from' => 'date',
        'expires_at' => 'date',
        'delivered_at' => 'datetime',
        'redeemed_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(VoucherOrderItem::class, 'voucher_order_item_id');
    }

    public function voucher(): BelongsTo
    {
        return $this->belongsTo(Voucher::class);
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function redemptions(): HasMany
    {
        return $this->hasMany(VoucherRedemption::class);
    }

    public function scopeValid(Builder $query): Builder
    {
        return $query->whereIn('status', ['active', 'partially_redeemed'])
            ->where(function (Builder $query): void {
                $query->whereNull('valid_from')->orWhere('valid_from', '<=', now()->toDateString());
            })
            ->where(function (Builder $query): void {
                $query->whereNull('expires_at')->orWhere('expires_at', '>=', now()->toDateString());
            });
    }

    public function scopeRedeemable(Builder $query): Builder
    {
        return $query->valid()->whereIn('status', ['active', 'partially_redeemed']);
    }

    public function scopeExpired(Builder $query): Builder
    {
        return $query->whereNotNull('expires_at')->where('expires_at', '<', now()->toDateString());
    }
}
