<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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
        'accommodation_type',
        'villa_code',

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

    public function features(): BelongsToMany
    {
        return $this->belongsToMany(
            AccommodationFeature::class,
            'accommodation_accommodation_feature',
            'accommodation_id',
            'accommodation_feature_id'
        )
            ->where('accommodation_features.is_active', true)
            ->orderBy('accommodation_features.sort_order')
            ->orderBy('accommodation_features.label')
            ->withTimestamps();
    }

    public function activeFeatures(): BelongsToMany
    {
        return $this->features();
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
        if (! empty($this->villa_code)) {
            return 'https://nandinijunglebyhanginggardens.reserve-online.net/?room=' . urlencode($this->villa_code);
        }

        return null;
    }

    public function getUrlPrefixAttribute(): string
    {
        return $this->accommodation_type === 'suite'
            ? 'the-royal-suites'
            : 'jungle-villas';
    }

    public function getShowUrlAttribute(): string
    {
        return route('accommodations.show', [
            'type' => $this->url_prefix,
            'accommodation' => $this->slug,
        ]);
    }
}
