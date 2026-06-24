<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Gallery extends Model
{
    protected $fillable = [
        'title',
        'slug',

        'category',

        'excerpt',
        'description',

        'image',
        'image_alt',

        'mobile_image',
        'mobile_image_alt',

        'button_label',
        'button_url',

        'meta_title',
        'meta_description',

        'is_featured',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected static function booted(): void
    {
        static::saving(function (Gallery $gallery): void {
            if (blank($gallery->slug) && filled($gallery->title)) {
                $gallery->slug = Str::slug($gallery->title);
            }
        });

        static::deleted(function (Gallery $gallery): void {
            foreach (['image', 'mobile_image'] as $attribute) {
                if (filled($gallery->{$attribute})) {
                    Storage::disk('public')->delete($gallery->{$attribute});
                }
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
