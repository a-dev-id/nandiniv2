<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VoucherRedemption extends Model
{
    use HasFactory;

    protected $fillable = [
        'issued_voucher_id',
        'redeemed_by_user_id',
        'redemption_location',
        'department',
        'reference_number',
        'amount',
        'balance_before',
        'balance_after',
        'notes',
        'redeemed_at',
    ];

    protected $casts = [
        'amount' => 'integer',
        'balance_before' => 'integer',
        'balance_after' => 'integer',
        'redeemed_at' => 'datetime',
    ];

    public function issuedVoucher(): BelongsTo
    {
        return $this->belongsTo(IssuedVoucher::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'redeemed_by_user_id');
    }
}
