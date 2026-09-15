<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class SignatureDish extends Model
{
    protected $fillable = ['name', 'slug', 'eyebrow', 'subtitle', 'price', 'short_description', 'cta_label', 'cta_url', 'image', 'image_alt', 'content', 'is_published', 'sort_order', 'meta_title', 'meta_description'];

    protected $casts = ['is_published' => 'boolean', 'sort_order' => 'integer'];

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function scopeInDisplayOrder(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    public function getImageUrlAttribute(): ?string
    {
        if (blank($this->image)) {
            return null;
        }

        if (str_starts_with($this->image, 'http://') || str_starts_with($this->image, 'https://') || str_starts_with($this->image, '/')) {
            return asset($this->image);
        }

        return Storage::disk('public')->exists($this->image) ? asset('storage/'.$this->image) : null;
    }

    public function sections(): HasMany
    {
        return $this->hasMany(SignatureDishSection::class)->orderBy('sort_order');
    }

    public function activeSections(): HasMany
    {
        return $this->hasMany(SignatureDishSection::class)
            ->where('is_active', true)
            ->orderBy('sort_order');
    }
}
