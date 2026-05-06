<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AccommodationFeature extends Model
{
    protected $fillable = [
        'label',
        'icon_image',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function accommodations(): BelongsToMany
    {
        return $this->belongsToMany(
            Accommodation::class,
            'accommodation_accommodation_feature',
            'accommodation_feature_id',
            'accommodation_id'
        )->withTimestamps();
    }
}
