<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Award extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'subtitle',
        'excerpt',
        'description',

        'award_name',
        'awarded_by',
        'award_year',
        'award_date',

        'hero_image',
        'hero_image_alt',
        'hero_mobile_image',
        'hero_mobile_image_alt',

        'card_image',
        'card_image_alt',

        'button_label',
        'button_url',

        'meta_title',
        'meta_description',

        'is_featured',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'award_date' => 'date',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected static function booted(): void
    {
        static::saving(function (Award $award): void {
            if (blank($award->slug) && filled($award->title)) {
                $award->slug = Str::slug($award->title);
            }
        });
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
