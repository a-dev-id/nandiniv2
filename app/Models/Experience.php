<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Experience extends Model
{
    public const DINING_INQUIRY_SLUGS = [
        'romantic-dining-by-the-chapel',
        'moonlit-jungle-romance',
        'riverside-romance',
    ];

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
        'opening_hours',
        'experience_type',
        'whatsapp_number',

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

    public function vouchers(): HasMany
    {
        return $this->hasMany(Voucher::class);
    }

    public function scopeDiningInquiryOptions(Builder $query): Builder
    {
        return $query
            ->where('is_active', true)
            ->whereIn('slug', self::DINING_INQUIRY_SLUGS)
            ->whereHas('vouchers', fn (Builder $query): Builder => $query
                ->where('is_active', true)
                ->whereHas('category', fn (Builder $query): Builder => $query
                    ->where('slug', 'signature-dining-experiences')
                    ->where('is_active', true)))
            ->orderBy('sort_order')
            ->orderBy('title');
    }

}
