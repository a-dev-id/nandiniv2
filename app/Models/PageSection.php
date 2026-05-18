<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PageSection extends Model
{
    protected $fillable = [
        'page_id',
        'section_key',
        'title',
        'subtitle',
        'excerpt',
        'description',

        'button_label',
        'button_link_type',
        'button_url',
        'button_route',

        'items',
        'background_color',

        'text_align',

        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'has_dark_overlay' => 'boolean',
        'items' => 'array',
    ];

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(PageSectionImage::class)->orderBy('sort_order');
    }
}
