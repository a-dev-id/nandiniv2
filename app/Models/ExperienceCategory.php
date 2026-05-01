<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExperienceCategory extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'subtitle',
        'excerpt',
        'description',
        'image',
        'image_alt',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function experiences(): HasMany
    {
        return $this->hasMany(Experience::class, 'experience_category_id')
            ->orderBy('sort_order');
    }
}
