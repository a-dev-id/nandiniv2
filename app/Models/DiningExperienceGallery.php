<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DiningExperienceGallery extends Model
{
    protected $table = 'dining_experience_gallery';

    protected $fillable = ['dining_experience_id', 'image', 'image_alt', 'caption', 'is_active', 'sort_order'];

    protected $casts = ['is_active' => 'boolean', 'sort_order' => 'integer'];

    public function diningExperience(): BelongsTo
    {
        return $this->belongsTo(DiningExperience::class);
    }
}
