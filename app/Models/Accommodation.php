<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Accommodation extends Model
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

        'size',
        'occupancy',
        'bed_type',
        'view',

        'button_label',
        'button_url',
        'booking_url_override',

        'meta_title',
        'meta_description',

        'is_featured',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function images(): HasMany
    {
        return $this->hasMany(AccommodationImage::class)
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function activeImages(): HasMany
    {
        return $this->images()
            ->where('is_active', true);
    }

    public function features(): HasMany
    {
        return $this->hasMany(AccommodationFeature::class)
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function activeFeatures(): HasMany
    {
        return $this->features()
            ->where('is_active', true);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('title');
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function getBookingUrlAttribute(): ?string
    {
        if (! empty($this->booking_url_override)) {
            return $this->booking_url_override;
        }

        if (! empty($this->button_url)) {
            return $this->button_url;
        }

        return null;
    }
}
