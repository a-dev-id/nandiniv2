<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class FestiveEvent extends Model
{
    protected $fillable = [
        'title', 'slug', 'is_active', 'sort_order', 'meta_title', 'meta_description',
        'hero_image', 'hero_image_alt', 'hero_eyebrow', 'hero_heading', 'hero_subheading',
        'hero_description', 'hero_price', 'hero_button_label', 'hero_button_url',
        'information_visible', 'information_items',
        'menu_visible', 'menu_eyebrow', 'menu_heading', 'menu_description', 'menu_items',
        'programme_visible', 'programme_image', 'programme_image_alt', 'programme_eyebrow',
        'programme_heading', 'programme_items',
        'reservation_visible', 'reservation_eyebrow', 'reservation_heading',
        'reservation_description', 'reservation_button_label', 'reservation_button_url',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'information_visible' => 'boolean',
        'information_items' => 'array',
        'menu_visible' => 'boolean',
        'menu_items' => 'array',
        'programme_visible' => 'boolean',
        'programme_items' => 'array',
        'reservation_visible' => 'boolean',
    ];

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
