<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Page extends Model
{
    public const SITE_MAIN = 'main';

    public const SITE_SPA = 'spa';

    protected $fillable = [
        'site',
        'page_name',
        'title',
        'slug',
        'subtitle',
        'excerpt',
        'description',
        'hero_image',
        'hero_image_alt',
        'hero_mobile_image',
        'hero_mobile_image_alt',
        'meta_title',
        'meta_description',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function scopeForMainSite(Builder $query): Builder
    {
        return $query->where('site', self::SITE_MAIN);
    }

    public function scopeForSpaSite(Builder $query): Builder
    {
        return $query->where('site', self::SITE_SPA);
    }

    public function sections(): HasMany
    {
        return $this->hasMany(PageSection::class)->orderBy('sort_order');
    }
}
