<?php

namespace App\Services\Affiliate\Click;

use App\Models\Affiliate;
use App\Models\AffiliateClickEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RecordAffiliateClickService
{
    public function __construct(
        private readonly BotDetector $bots,
        private readonly DeviceDetector $devices,
        private readonly CountryResolver $countries,
        private readonly ReferrerNormalizer $referrers,
        private readonly VisitorHasher $visitors,
    ) {}

    public function record(Affiliate $affiliate, Request $request): AffiliateClickEvent
    {
        $clickedAt = now();
        $clickDate = $clickedAt->toDateString();
        $bot = $this->bots->detect($request->userAgent());
        $visitorHash = $this->visitors->hash($affiliate, $request);
        $country = $this->countries->resolve($request);
        $device = $this->devices->detect($request, $bot->isBot);
        $referrer = $this->referrers->normalize($request);

        return DB::transaction(function () use ($affiliate, $clickedAt, $clickDate, $bot, $visitorHash, $country, $device, $referrer): AffiliateClickEvent {
            $isUnique = false;

            if (! $bot->isBot) {
                $timestamp = now();
                $isUnique = DB::table('affiliate_unique_clicks')->insertOrIgnore([
                    'affiliate_id' => $affiliate->getKey(),
                    'visitor_hash' => $visitorHash,
                    'click_date' => $clickDate,
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ]) === 1;
            }

            return AffiliateClickEvent::query()->create([
                'affiliate_id' => $affiliate->getKey(),
                'clicked_at' => $clickedAt,
                'click_date' => $clickDate,
                'country_code' => $country['code'],
                'country_name' => $country['name'],
                'device_type' => $device,
                'referrer_domain' => $referrer,
                'visitor_hash' => $visitorHash,
                'is_unique' => $isUnique,
                'is_bot' => $bot->isBot,
                'bot_name' => $bot->name,
            ]);
        });
    }
}
