<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VoucherPaymentEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'voucher_order_id',
        'gateway',
        'gateway_payment_id',
        'event_fingerprint',
        'event_type',
        'gateway_status',
        'signature_valid',
        'payload',
        'processed_at',
        'processing_error',
    ];

    protected $casts = [
        'signature_valid' => 'boolean',
        'payload' => 'array',
        'processed_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(VoucherOrder::class, 'voucher_order_id');
    }
}
