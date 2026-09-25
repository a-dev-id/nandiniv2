<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class SignatureDish extends Model
{
    protected $fillable = ['name', 'slug', 'eyebrow', 'subtitle', 'price', 'short_description', 'cta_label', 'cta_url', 'image', 'image_alt', 'content', 'detail_content', 'is_published', 'sort_order', 'meta_title', 'meta_description'];

    protected $casts = [
        'detail_content' => 'array',
        'is_published' => 'boolean',
        'sort_order' => 'integer',
    ];

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
        return $this->resolveImageUrl($this->image);
    }

    /** @return array<string, mixed> */
    public function getDetailAttribute(): array
    {
        $detailContent = $this->detail_content ?? [];

        return is_array($detailContent) && isset($detailContent[0]) && is_array($detailContent[0])
            ? $detailContent[0]
            : [];
    }

    public function resolveImageUrl(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        if (str_starts_with($path, '/')) {
            return asset($path);
        }

        $publicStoragePath = public_path('storage/'.ltrim($path, '/'));

        return is_file($publicStoragePath) || Storage::disk('public')->exists($path)
            ? asset('storage/'.$path)
            : null;
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
