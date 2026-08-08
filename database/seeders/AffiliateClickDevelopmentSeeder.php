<?php

namespace Database\Seeders;

use App\Models\Affiliate;
use App\Models\AffiliateClickEvent;
use App\Models\AffiliateUniqueClick;
use Illuminate\Database\Seeder;

class AffiliateClickDevelopmentSeeder extends Seeder
{
    public function run(): void
    {
        Affiliate::query()->where('status', 'approved')->each(function (Affiliate $affiliate): void {
            if ($affiliate->clickEvents()->exists()) {
                return;
            }

            $fixtures = [
                [0, 'ID', 'Indonesia', 'mobile', 'instagram.com', true, false, null, 'visitor-a'],
                [0, 'ID', 'Indonesia', 'mobile', 'instagram.com', false, false, null, 'visitor-a'],
                [1, 'AU', 'Australia', 'desktop', 'google.com', true, false, null, 'visitor-b'],
                [3, 'SG', 'Singapore', 'tablet', null, true, false, null, 'visitor-c'],
                [7, null, null, 'unknown', null, true, false, null, 'visitor-d'],
                [12, 'US', 'United States', 'desktop', 'facebook.com', true, false, null, 'visitor-e'],
                [2, 'ID', 'Indonesia', 'unknown', null, false, true, 'facebook', 'preview-a'],
                [8, 'ID', 'Indonesia', 'unknown', null, false, true, 'googlebot', 'crawler-a'],
            ];

            foreach ($fixtures as [$daysAgo, $countryCode, $countryName, $device, $referrer, $isUnique, $isBot, $botName, $visitor]) {
                $clickedAt = now()->subDays($daysAgo)->setTime(10 + ($daysAgo % 8), 15);
                $visitorHash = hash_hmac('sha256', $affiliate->getKey().'|local-fixture|'.$visitor, (string) config('affiliate-clicks.visitor_hash_key'));

                if ($isUnique && ! $isBot) {
                    AffiliateUniqueClick::query()->firstOrCreate([
                        'affiliate_id' => $affiliate->getKey(),
                        'visitor_hash' => $visitorHash,
                        'click_date' => $clickedAt->toDateString(),
                    ]);
                }

                AffiliateClickEvent::query()->create([
                    'affiliate_id' => $affiliate->getKey(),
                    'clicked_at' => $clickedAt,
                    'click_date' => $clickedAt->toDateString(),
                    'country_code' => $countryCode,
                    'country_name' => $countryName,
                    'device_type' => $device,
                    'referrer_domain' => $referrer,
                    'visitor_hash' => $visitorHash,
                    'is_unique' => $isUnique,
                    'is_bot' => $isBot,
                    'bot_name' => $botName,
                ]);
            }
        });
    }
}
