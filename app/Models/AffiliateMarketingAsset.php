<?php

namespace App\Models;

use App\Enums\AffiliateMarketingAssetType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class AffiliateMarketingAsset extends Model
{
    protected $fillable = [
        'title', 'description', 'asset_type', 'file_path', 'external_url', 'thumbnail_path',
        'file_name', 'file_extension', 'file_size', 'is_active', 'is_featured',
        'available_from', 'available_until', 'sort_order', 'created_by', 'updated_by',
    ];

    protected $casts = [
        'asset_type' => AffiliateMarketingAssetType::class,
        'file_size' => 'integer',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'available_from' => 'datetime',
        'available_until' => 'datetime',
        'sort_order' => 'integer',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $asset): void {
            if (blank($asset->file_path) && blank($asset->external_url)) {
                throw ValidationException::withMessages(['file_path' => 'An uploaded file or approved external URL is required.']);
            }

            if (filled($asset->external_url)
                && (filter_var($asset->external_url, FILTER_VALIDATE_URL) === false || parse_url($asset->external_url, PHP_URL_SCHEME) !== 'https')) {
                throw ValidationException::withMessages(['external_url' => 'The external URL must be a valid HTTPS URL.']);
            }

            $type = $asset->asset_type instanceof AffiliateMarketingAssetType
                ? $asset->asset_type
                : AffiliateMarketingAssetType::from((string) $asset->asset_type);
            $extension = strtolower(pathinfo((string) $asset->file_path, PATHINFO_EXTENSION));
            $allowedExtensions = match ($type) {
                AffiliateMarketingAssetType::Image, AffiliateMarketingAssetType::Banner, AffiliateMarketingAssetType::SocialMedia => ['jpg', 'jpeg', 'png', 'webp'],
                AffiliateMarketingAssetType::Document => ['pdf'],
                AffiliateMarketingAssetType::Video => [],
                default => ['jpg', 'jpeg', 'png', 'webp', 'pdf'],
            };

            if (filled($asset->file_path) && ! in_array($extension, $allowedExtensions, true)) {
                throw ValidationException::withMessages(['file_path' => 'The uploaded file type is not allowed for this material type.']);
            }

            if (filled($asset->thumbnail_path) && ! in_array(strtolower(pathinfo($asset->thumbnail_path, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'webp'], true)) {
                throw ValidationException::withMessages(['thumbnail_path' => 'The preview image must be JPG, PNG, or WEBP.']);
            }

            if (auth()->check()) {
                $asset->updated_by = auth()->id();
                $asset->created_by ??= auth()->id();
            }

            if ($asset->file_path && Storage::disk('local')->exists($asset->file_path)) {
                $asset->file_name ??= basename($asset->file_path);
                $asset->file_extension = strtolower(pathinfo($asset->file_path, PATHINFO_EXTENSION));
                $asset->file_size = Storage::disk('local')->size($asset->file_path);
            } else {
                $asset->file_name = null;
                $asset->file_extension = null;
                $asset->file_size = null;
            }
        });

        static::deleting(function (self $asset): void {
            foreach (array_filter([$asset->file_path, $asset->thumbnail_path]) as $path) {
                Storage::disk('local')->delete($path);
            }
        });
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->where(fn (Builder $query): Builder => $query->whereNull('available_from')->orWhere('available_from', '<=', now()))
            ->where(fn (Builder $query): Builder => $query->whereNull('available_until')->orWhere('available_until', '>=', now()));
    }

    public function isAvailable(): bool
    {
        return $this->is_active
            && ($this->available_from === null || $this->available_from->lte(now()))
            && ($this->available_until === null || $this->available_until->gte(now()));
    }
}
