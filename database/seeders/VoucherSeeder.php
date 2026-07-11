<?php

namespace Database\Seeders;

use App\Models\Experience;
use App\Models\Voucher;
use App\Models\VoucherCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class VoucherSeeder extends Seeder
{
    public function run(): void
    {
        Voucher::query()
            ->whereIn('slug', [
                'monetary-gift-voucher',
                'romantic-dining-voucher',
                'spa-experience-voucher',
                'jungle-stay-voucher',
                'panoramic-jacuzzi-royal-suite-voucher',
                'nandini-experience-voucher',
            ])
            ->update(['is_active' => false, 'is_featured' => false]);

        VoucherCategory::query()
            ->whereIn('slug', ['stay', 'gift'])
            ->update(['is_active' => false]);

        Experience::query()
            ->with(['category', 'prices' => fn($query) => $query->where('is_active', true)->orderBy('sort_order')])
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->each(function (Experience $experience): void {
                $price = $experience->prices->first();

                if (! $price || (float) $price->price <= 0) {
                    return;
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
                $voucherType = match (true) {
                    str_contains(strtolower($categoryName . ' ' . $experience->title), 'spa') => 'spa',
                    str_contains(strtolower($categoryName . ' ' . $experience->title), 'dining') => 'dining',
                    default => 'experience',
                };

                $sellingPrice = (int) round((float) $price->price);

                Voucher::query()->updateOrCreate(
                    ['slug' => $slug],
                    [
                        'voucher_category_id' => $category->id,
                        'title' => $experience->title,
                        'sku' => 'EXP-' . strtoupper(Str::slug($experience->slug ?: $experience->title, '')),
                        'excerpt' => $experience->excerpt ?: $experience->subtitle,
                        'description' => $experience->description,
                        'inclusions' => $experience->inclusions,
                        'terms_conditions' => '<p>Advance reservation is required. Voucher is subject to availability and cannot be exchanged for cash.</p>',
                        'image' => $experience->image,
                        'card_image' => $experience->card_image ?: $experience->image,
                        'image_alt' => $experience->card_image_alt ?: $experience->image_alt ?: $experience->title,
                        'voucher_type' => $voucherType,
                        'face_value' => null,
                        'selling_price' => $sellingPrice,
                        'currency' => substr((string) ($price->currency ?: 'IDR'), 0, 3),
                        'price_type' => $price->price_type,
                        'unit_type' => $price->unit_type,
                        'validity_type' => 'days_after_issue',
                        'validity_days' => 365,
                        'minimum_quantity' => 1,
                        'purchase_limit_per_order' => $price->max_qty ?: 10,
                        'is_featured' => (bool) $experience->is_featured,
                        'is_active' => true,
                        'sort_order' => (int) $experience->sort_order,
                        'meta_title' => $experience->meta_title,
                        'meta_description' => $experience->meta_description,
                    ]
                );
            });

        VoucherCategory::query()
            ->whereDoesntHave('vouchers', fn($query) => $query->active())
            ->update(['is_active' => false]);
    }

    private function cleanText(?string $value): ?string
    {
        $text = trim((string) preg_replace('/\s+/', ' ', strip_tags((string) $value)));

        return $text === '' ? null : $text;
    }
}
