<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use InvalidArgumentException;

class Voucher extends Model
{
    use HasFactory;
    use SoftDeletes;

    public const TYPES = ['monetary', 'accommodation', 'dining', 'spa', 'experience', 'custom'];

    public const VALIDITY_TYPES = ['days_after_issue', 'fixed_date', 'manual'];

    protected $fillable = [
        'voucher_category_id',
        'experience_id',
        'title',
        'slug',
        'sku',
        'excerpt',
        'description',
        'inclusions',
        'terms_conditions',
        'image',
        'card_image',
        'gallery_images',
        'image_alt',
        'voucher_type',
        'face_value',
        'selling_price',
        'price_options',
        'discount_percentage',
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
        'gallery_images' => 'array',
        'face_value' => 'integer',
        'selling_price' => 'integer',
        'price_options' => 'array',
        'discount_percentage' => 'integer',
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

    public function experience(): BelongsTo
    {
        return $this->belongsTo(Experience::class);
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

    public function scopeCheapestFirst(Builder $query): Builder
    {
        return $query
            ->orderByRaw('selling_price * (100 - COALESCE(discount_percentage, 0)) ASC')
            ->orderBy('sort_order')
            ->orderBy('title');
    }

    public function getPreviewImageAttribute(): ?string
    {
        return $this->card_image ?: $this->image;
    }

    public function getPurchasableAttribute(): bool
    {
        return (bool) $this->is_active && $this->discounted_price > 0;
    }

    public function getHasDiscountAttribute(): bool
    {
        return $this->discount_percentage > 0;
    }

    public function getDiscountedPriceAttribute(): int
    {
        return $this->discountedPriceForOption();
    }

    public function availablePriceOptions(): array
    {
        return collect($this->price_options ?? [])
            ->map(fn (mixed $option, int|string $index): array => [
                'key' => (string) data_get($option, 'key', 'option-'.$index),
                'label' => trim((string) data_get($option, 'label')),
                'additional_price' => max(0, (int) data_get($option, 'additional_price', 0)),
            ])
            ->filter(fn (array $option): bool => $option['label'] !== '')
            ->values()
            ->all();
    }

    public function resolvePriceOption(?string $key = null): ?array
    {
        if (blank($key)) {
            return null;
        }

        $options = $this->availablePriceOptions();

        if ($options === []) {
            throw new InvalidArgumentException('The selected room option is not available.');
        }

        foreach ($options as $option) {
            if (hash_equals($option['key'], $key)) {
                return $option;
            }
        }

        throw new InvalidArgumentException('The selected room option is not available.');
    }

    public function originalPriceForOption(?array $option = null): int
    {
        return max(0, (int) $this->selling_price + (int) ($option['additional_price'] ?? 0));
    }

    public function discountedPriceForOption(?array $option = null): int
    {
        $percentage = min(100, max(0, (int) $this->discount_percentage));

        return (int) round($this->originalPriceForOption($option) * (100 - $percentage) / 100);
    }
}
