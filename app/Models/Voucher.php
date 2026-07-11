<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Voucher extends Model
{
    use HasFactory;
    use SoftDeletes;

    public const TYPES = ['monetary', 'accommodation', 'dining', 'spa', 'experience', 'custom'];
    public const VALIDITY_TYPES = ['days_after_issue', 'fixed_date', 'manual'];

    protected $fillable = [
        'voucher_category_id',
        'title',
        'slug',
        'sku',
        'excerpt',
        'description',
        'inclusions',
        'terms_conditions',
        'image',
        'card_image',
        'image_alt',
        'voucher_type',
        'face_value',
        'selling_price',
        'currency',
        'price_type',
        'unit_type',
        'validity_type',
        'validity_days',
        'fixed_valid_from',
        'fixed_valid_until',
        'minimum_quantity',
        'maximum_quantity',
        'purchase_limit_per_order',
        'allow_partial_redemption',
        'is_featured',
        'is_active',
        'sort_order',
        'meta_title',
        'meta_description',
    ];

    protected $casts = [
        'face_value' => 'integer',
        'selling_price' => 'integer',
        'validity_days' => 'integer',
        'fixed_valid_from' => 'date',
        'fixed_valid_until' => 'date',
        'minimum_quantity' => 'integer',
        'maximum_quantity' => 'integer',
        'purchase_limit_per_order' => 'integer',
        'allow_partial_redemption' => 'boolean',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(VoucherCategory::class, 'voucher_category_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('title');
    }

    public function getPreviewImageAttribute(): ?string
    {
        return $this->card_image ?: $this->image;
    }

    public function getPurchasableAttribute(): bool
    {
        return (bool) $this->is_active && $this->selling_price > 0;
    }
}
