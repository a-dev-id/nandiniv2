<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Reward extends Model
{
    protected $fillable = [
        'reward_category_id',
        'title',
        'slug',
        'excerpt',
        'description',
        'image',
        'image_alt',
        'points_required',
        'points_label',
        'button_label',
        'button_url',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'points_required' => 'integer',
        'sort_order' => 'integer',
    ];

    protected static function booted(): void
    {
        static::saving(function (Reward $reward) {
            if (blank($reward->slug) && filled($reward->title)) {
                $reward->slug = Str::slug($reward->title);
            }

            if (blank($reward->points_label) && $reward->points_required > 0) {
                $reward->points_label = number_format($reward->points_required) . ' POINTS';
            }
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(RewardCategory::class, 'reward_category_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id');
    }
}
