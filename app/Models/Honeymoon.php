<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Honeymoon extends Model
{
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

    protected static function booted(): void
    {
        static::saving(function (Honeymoon $honeymoon) {
            if (blank($honeymoon->slug) && filled($honeymoon->title)) {
                $honeymoon->slug = Str::slug($honeymoon->title);
            }

            if (blank($honeymoon->meta_title) && filled($honeymoon->title)) {
                $honeymoon->meta_title = $honeymoon->title;
            }

            if (blank($honeymoon->button_label)) {
                $honeymoon->button_label = 'Book Now';
            }
        });
    }

    public function scopePublished(Builder $query): Builder
    {
        $today = today();

        return $query
            ->where('is_active', true)
            ->where(function (Builder $query) use ($today) {
                $query
                    ->whereNull('valid_start_date')
                    ->orWhereDate('valid_start_date', '<=', $today);
            })
            ->where(function (Builder $query) use ($today) {
                $query
                    ->whereNull('valid_end_date')
                    ->orWhereDate('valid_end_date', '>=', $today);
            });
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function getResolvedBookingUrlAttribute(): ?string
    {
        if (filled($this->booking_url_override)) {
            return $this->booking_url_override;
        }

        if (filled($this->button_url)) {
            return $this->button_url;
        }

        return null;
    }

    public function getResolvedCardImageAttribute(): ?string
    {
        return $this->card_image ?: $this->hero_image;
    }

    public function getResolvedHeroImageAttribute(): ?string
    {
        return $this->hero_image ?: $this->card_image;
    }

    public function getResolvedHeroMobileImageAttribute(): ?string
    {
        return $this->hero_mobile_image ?: $this->hero_image ?: $this->card_image;
    }
}
