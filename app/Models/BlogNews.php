<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class BlogNews extends Model
{
    protected $table = 'blog_news';

    protected $fillable = [
        'type',

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

        'author_name',
        'published_at',

        'button_label',
        'button_url',

        'meta_title',
        'meta_description',

        'is_featured',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'published_at' => 'date',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected static function booted(): void
    {
        static::saving(function (BlogNews $blogNews): void {
            if (blank($blogNews->slug) && filled($blogNews->title)) {
                $blogNews->slug = Str::slug($blogNews->title);
            }
        });
    }

    public function sections(): HasMany
    {
        return $this->hasMany(BlogNewsSection::class)
            ->orderBy('sort_order');
    }

    public function activeSections(): HasMany
    {
        return $this->hasMany(BlogNewsSection::class)
            ->where('is_active', true)
            ->orderBy('sort_order');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('is_active', true)
            ->where(function (Builder $query): void {
                $query
                    ->whereNull('published_at')
                    ->orWhereDate('published_at', '<=', today());
            });
    }

    public function scopeBlog(Builder $query): Builder
    {
        return $query->where('type', 'blog');
    }

    public function scopeNews(Builder $query): Builder
    {
        return $query->where('type', 'news');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
