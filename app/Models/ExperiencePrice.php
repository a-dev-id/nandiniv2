<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExperiencePrice extends Model
{
    protected $fillable = [
        'experience_id',
        'label',
        'price',
        'currency',
        'price_type',
        'unit_type',
        'min_qty',
        'max_qty',
        'notes',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function experience(): BelongsTo
    {
        return $this->belongsTo(Experience::class);
    }
}
