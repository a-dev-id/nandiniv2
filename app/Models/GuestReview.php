<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class GuestReview extends Model
{
    protected $fillable = [
        'reviewer_name',
        'review_text',
        'rating',
        'reviewed_at',
        'source',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'rating' => 'integer',
        'reviewed_at' => 'date',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
