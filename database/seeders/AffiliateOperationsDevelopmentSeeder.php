<?php

namespace Database\Seeders;

use App\Enums\AffiliateMarketingAssetType;
use App\Models\AffiliateMarketingAsset;
use Illuminate\Database\Seeder;

class AffiliateOperationsDevelopmentSeeder extends Seeder
{
    public function run(): void
    {
        abort_unless(app()->environment('local'), 403, 'Affiliate operations fixtures are development-only.');

        foreach ([
            ['title' => 'Synthetic Featured Photography', 'asset_type' => AffiliateMarketingAssetType::Image, 'external_url' => 'https://example.test/synthetic-photography', 'is_featured' => true, 'sort_order' => 10],
            ['title' => 'Synthetic Brand Video', 'asset_type' => AffiliateMarketingAssetType::Video, 'external_url' => 'https://example.test/synthetic-video', 'is_featured' => false, 'sort_order' => 20],
            ['title' => 'Synthetic Seasonal Offer', 'asset_type' => AffiliateMarketingAssetType::SpecialOffer, 'external_url' => 'https://example.test/synthetic-offer', 'is_featured' => false, 'sort_order' => 30],
        ] as $fixture) {
            AffiliateMarketingAsset::query()->updateOrCreate(['title' => $fixture['title']], [
                ...$fixture,
                'description' => 'Synthetic local-only material for Affiliate operations testing.',
                'is_active' => true,
                'available_from' => now()->subDay(),
                'available_until' => now()->addMonth(),
            ]);
        }
    }
}
