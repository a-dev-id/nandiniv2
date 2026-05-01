<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Experience extends Model
{
    protected $fillable = [
        'experience_category_id',
        'title',
        'slug',
        'subtitle',
        'excerpt',
        'description',
        'inclusions',
        'duration',
        'location',

        'image',
        'image_alt',
        'card_image',
        'card_image_alt',

        'is_featured',
        'is_active',
        'sort_order',
        'meta_title',
        'meta_description',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(ExperienceCategory::class, 'experience_category_id');
    }

    public function prices(): HasMany
    {
        return $this->hasMany(ExperiencePrice::class)
            ->orderBy('sort_order');
    }
}
