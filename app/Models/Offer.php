<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    private const RESERVE_URL = 'https://nandinijunglebyhanginggardens.reserve-online.net/?checkin=2026-07-01&rooms=1&nights=2&adults=2&rate=942373';

    protected $fillable = [
        'title',
        'slug',
        'subtitle',
        'excerpt',
        'description',

        'hero_image',
        'hero_image_alt',
        'hero_mobile_image',
        'hero_mobile_image_alt',

        'card_image',
        'card_image_alt',

        'valid_start_date',
        'valid_end_date',

        'booking_checkin_date',
        'booking_nights',
        'booking_rooms',
        'booking_adults',
        'booking_rate_code',
        'booking_bkcode',
        'booking_url_override',

        'button_label',
        'button_url',

        'is_featured',
        'is_active',

        'meta_title',
        'meta_description',

        'sort_order',
    ];

    protected $casts = [
        'valid_start_date' => 'date',
        'valid_end_date' => 'date',

        'booking_checkin_date' => 'date',
        'booking_nights' => 'integer',
        'booking_rooms' => 'integer',
        'booking_adults' => 'integer',

        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('is_active', true)
            ->where(function (Builder $query): void {
                $query
                    ->whereNull('valid_start_date')
                    ->orWhereDate('valid_start_date', '<=', now());
            })
            ->where(function (Builder $query): void {
                $query
                    ->whereNull('valid_end_date')
                    ->orWhereDate('valid_end_date', '>=', now());
            });
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function getBookingUrlAttribute(): string
    {
        return self::RESERVE_URL;
    }
}
