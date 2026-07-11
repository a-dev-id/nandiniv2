<?php

namespace App\Services\Voucher;

use App\Models\Experience;
use App\Models\Voucher;
use App\Models\VoucherCategory;
use Illuminate\Support\Str;

class ExperienceVoucherSynchronizer
{
    public function synchronize(): int
    {
        $synchronized = 0;

        Experience::query()
            ->with(['category', 'prices' => fn ($query) => $query->where('is_active', true)->orderBy('sort_order')])
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->each(function (Experience $experience) use (&$synchronized): void {
                if ($this->synchronizeExperience($experience)) {
                    $synchronized++;
                }
            });

        VoucherCategory::query()
            ->whereDoesntHave('vouchers', fn ($query) => $query->active())
            ->update(['is_active' => false]);

        return $synchronized;
    }

    public function synchronizeExperience(Experience $experience): ?Voucher
    {
        $price = $experience->prices->first();

        if (! $price || (float) $price->price <= 0) {
            return null;
        }

        $categoryName = $experience->category?->name ?: 'Experience';
        $category = VoucherCategory::query()->updateOrCreate(
            ['slug' => Str::slug($categoryName)],
            [
                'name' => $categoryName,
                'description' => $this->cleanText($experience->category?->excerpt ?: $experience->category?->description),
                'image' => $experience->category?->image,
                'image_alt' => $experience->category?->image_alt,
                'is_active' => true,
            ]
        );

        $slug = Str::slug($experience->slug ?: $experience->title);
        $voucher = Voucher::query()->where('experience_id', $experience->id)->first()
            ?? Voucher::query()->where('slug', $slug)->first();

        $defaults = [
            'validity_type' => 'days_after_issue',
            'validity_days' => 365,
            'minimum_quantity' => 1,
            'discount_percentage' => 0,
            'is_featured' => (bool) $experience->is_featured,
            'is_active' => true,
        ];

        $voucher ??= new Voucher($defaults);
        $voucher->fill([
            'experience_id' => $experience->id,
            'voucher_category_id' => $category->id,
            'title' => $experience->title,
            'slug' => $slug,
            'sku' => 'EXP-' . strtoupper(Str::slug($experience->slug ?: $experience->title, '')),
            'excerpt' => $experience->excerpt ?: $experience->subtitle,
            'description' => $experience->description,
            'image' => $experience->image,
            'card_image' => $experience->card_image ?: $experience->image,
            'image_alt' => $experience->card_image_alt ?: $experience->image_alt ?: $experience->title,
            'voucher_type' => $this->voucherType($categoryName, $experience->title),
            'face_value' => null,
            'selling_price' => (int) round((float) $price->price),
            'currency' => substr((string) ($price->currency ?: 'IDR'), 0, 3),
            'price_type' => $price->price_type,
            'unit_type' => $price->unit_type,
            'purchase_limit_per_order' => $price->max_qty ?: 10,
            'sort_order' => (int) $experience->sort_order,
            'meta_title' => $experience->meta_title,
            'meta_description' => $experience->meta_description,
        ]);

        if (! $voucher->exists) {
            $voucher->fill($defaults);
            $voucher->terms_conditions = '<p>Advance reservation is required. Voucher is subject to availability and cannot be exchanged for cash.</p>';
        }

        $voucher->save();

        return $voucher;
    }

    private function voucherType(string $category, string $title): string
    {
        return match (true) {
            str_contains(strtolower($category . ' ' . $title), 'spa') => 'spa',
            str_contains(strtolower($category . ' ' . $title), 'dining') => 'dining',
            default => 'experience',
        };
    }

    private function cleanText(?string $value): ?string
    {
        $text = trim((string) preg_replace('/\s+/', ' ', strip_tags((string) $value)));

        return $text === '' ? null : $text;
    }
}
