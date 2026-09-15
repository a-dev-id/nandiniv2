<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DiningExperience extends Model
{
    protected $fillable = [
        'title', 'slug', 'card_title', 'short_description', 'card_cta_label',
        'description', 'card_image', 'card_image_alt', 'hero_image',
        'hero_mobile_image', 'hero_image_alt', 'intro_eyebrow', 'page_heading',
        'menu_cta_label', 'menu_url', 'reservation_cta_label', 'reservation_url',
        'opening_hours', 'experience_type', 'location', 'whatsapp_number',
        'is_active', 'sort_order', 'meta_title', 'meta_description',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function gallery(): HasMany
    {
        return $this->hasMany(DiningExperienceGallery::class)->orderBy('sort_order');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeInDisplayOrder(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    public function getDisplayTitleAttribute(): string
    {
        return $this->card_title ?: $this->title;
    }
}
