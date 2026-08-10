<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AffiliateExchangeRate extends Model
{
    protected $fillable = [
        'base_currency',
        'quote_currency',
        'base_units_per_quote',
        'is_active',
        'effective_at',
        'updated_by',
    ];

    protected $casts = [
        'base_units_per_quote' => 'decimal:6',
        'is_active' => 'boolean',
        'effective_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $rate): void {
            $rate->base_currency = mb_strtoupper($rate->base_currency ?: 'IDR');
            $rate->quote_currency = mb_strtoupper($rate->quote_currency);
            $rate->updated_by = auth('web')->id() ?: $rate->updated_by;
        });
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
