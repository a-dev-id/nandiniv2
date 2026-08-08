<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class AffiliateClickEvent extends Model
{
    protected $fillable = [
        'affiliate_id',
        'clicked_at',
        'click_date',
        'country_code',
        'country_name',
        'device_type',
        'referrer_domain',
        'visitor_hash',
        'is_unique',
        'is_bot',
        'bot_name',
    ];

    protected $hidden = ['visitor_hash'];

    protected $casts = [
        'clicked_at' => 'datetime',
        'is_unique' => 'boolean',
        'is_bot' => 'boolean',
    ];

    public function affiliate(): BelongsTo
    {
        return $this->belongsTo(Affiliate::class);
    }

    protected function clickDate(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value): ?Carbon => $value ? Carbon::parse($value)->startOfDay() : null,
            set: fn ($value): ?string => $value ? Carbon::parse($value)->toDateString() : null,
        );
    }
}
