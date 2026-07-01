<?php

namespace App\Models;

use App\Support\MemberBookingVoucher;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;
use Throwable;

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
        'hero_image_file_name',
        'hero_image_alt',
        'hero_mobile_image',
        'hero_mobile_image_file_name',
        'hero_mobile_image_alt',

        'card_image',
        'card_image_file_name',
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
        if (filled($this->booking_url_override)) {
            return html_entity_decode((string) $this->booking_url_override, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }

        $query = array_filter([
            'checkin' => $this->resolveBookingCheckinDate(),
            'nights' => $this->booking_nights,
            'rooms' => $this->booking_rooms,
            'adults' => $this->booking_adults,
            'rate' => $this->booking_rate_code,
            'bkcode' => $this->booking_bkcode,
        ], fn($value) => filled($value));

        if (! empty($query)) {
            return 'https://nandinijunglebyhanginggardens.reserve-online.net/?' . http_build_query($query);
        }

        return self::RESERVE_URL;
    }

    public function getResolvedButtonUrlAttribute(): ?string
    {
        if (filled($this->button_url)) {
            return $this->resolveButtonRouteOrUrl($this->button_url);
        }

        return MemberBookingVoucher::appendToUrl($this->booking_url);
    }

    private function resolveButtonRouteOrUrl(string $value): string
    {
        $value = trim(html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8'));

        if (Route::has($value)) {
            try {
                return route($value);
            } catch (Throwable) {
                return $value;
            }
        }

        return MemberBookingVoucher::appendToUrl($value);
    }

    private function resolveBookingCheckinDate(): ?string
    {
        if (empty($this->booking_checkin_date)) {
            return null;
        }

        $checkinDate = $this->booking_checkin_date instanceof Carbon
            ? $this->booking_checkin_date
            : Carbon::parse($this->booking_checkin_date);

        return $checkinDate->isPast()
            ? today()->toDateString()
            : $checkinDate->toDateString();
    }
}
