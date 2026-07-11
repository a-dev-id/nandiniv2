<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VoucherOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'voucher_order_id',
        'voucher_id',
        'voucher_title',
        'voucher_sku',
        'voucher_type',
        'quantity',
        'unit_price',
        'line_total',
        'currency',
        'recipient_name',
        'recipient_email',
        'personal_message',
        'delivery_method',
        'scheduled_delivery_at',
        'voucher_snapshot',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'integer',
        'line_total' => 'integer',
        'scheduled_delivery_at' => 'datetime',
        'voucher_snapshot' => 'array',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(VoucherOrder::class, 'voucher_order_id');
    }

    public function voucher(): BelongsTo
    {
        return $this->belongsTo(Voucher::class);
    }

    public function issuedVouchers(): HasMany
    {
        return $this->hasMany(IssuedVoucher::class);
    }
}
