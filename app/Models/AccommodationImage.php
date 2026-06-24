<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class AccommodationImage extends Model
{
    protected $fillable = [
        'accommodation_id',
        'image',
        'image_alt',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::deleted(function (AccommodationImage $image): void {
            if (filled($image->image)) {
                Storage::disk('public')->delete($image->image);
            }
        });
    }

    public function accommodation(): BelongsTo
    {
        return $this->belongsTo(Accommodation::class);
    }
}
