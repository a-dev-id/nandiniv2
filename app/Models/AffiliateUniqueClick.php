<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class AffiliateUniqueClick extends Model
{
    protected $fillable = ['affiliate_id', 'visitor_hash', 'click_date'];

    protected $hidden = ['visitor_hash'];

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
