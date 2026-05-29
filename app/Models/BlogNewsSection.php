<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BlogNewsSection extends Model
{
    protected $fillable = [
        'blog_news_id',
        'section_key',

        'title',
        'subtitle',
        'excerpt',
        'description',

        'video_url',
        'video_label',

        'button_label',
        'button_link_type',
        'button_url',
        'button_route',

        'text_align',
        'background_color',

        'items',

        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'items' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function blogNews(): BelongsTo
    {
        return $this->belongsTo(BlogNews::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(BlogNewsSectionImage::class)->orderBy('sort_order');
    }
}
