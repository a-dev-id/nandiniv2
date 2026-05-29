<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Spa extends Model
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
        'valid_start_date' => 'date',
        'valid_end_date' => 'date',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected static function booted(): void
    {
        static::saving(function (Spa $spa) {
            if (blank($spa->slug) && filled($spa->title)) {
                $spa->slug = Str::slug($spa->title);
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

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
