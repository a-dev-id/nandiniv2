<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class GuestReview extends Model
{
    protected $fillable = [
        'reviewer_name',
        'review_text',
        'excerpt',
        'rating',
        'reviewed_at',
        'source',
        'is_active',
        'is_featured',
        'show_on_dining',
        'sort_order',
    ];

    protected $casts = [
        'rating' => 'integer',
        'reviewed_at' => 'date',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'show_on_dining' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function scopeForDining(Builder $query): Builder
    {
        return $query->where('show_on_dining', true);
    }
}
