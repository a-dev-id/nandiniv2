<?php

namespace App\Http\Controllers;

use App\Models\Spa;
use App\Models\SpaSetting;
use App\Models\Voucher;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class SpaLandingController extends Controller
{
    private const MYSTICAL_JOURNEY_SLUG = 'a-mystical-journey-at-nandini-4d3n-wellness-retreat';

    private const DEFAULT_HERO_IMAGE = 'pages/hero/fb4a52d4-35a1-4c29-804d-100e00dd6b89.webp';

    private const DEFAULT_PHILOSOPHY_IMAGE = 'pages/sections/7bdab6e8-62b3-416a-85fb-3419a6a15ee8.webp';

    private const DEFAULT_RIVERSIDE_IMAGE = 'pages/sections/ea97fbfe-7ca1-43fe-8e30-51b1e9dcea01.webp';

    private const DEFAULT_GUEST_REVIEW_IMAGE = 'spas/hero/68d37345-f6e6-4f1d-a962-725cf049fe62.webp';

    private const MYSTICAL_JOURNEY_BOOKING_URL = 'https://nandinijunglebyhanginggardens.reserve-online.net/?nights=3&bkcode=wellness';

    public function __invoke(): View
    {
        $spaSettings = Schema::hasTable('spa_settings') ? SpaSetting::query()->first() : null;
        $sourceJourney = $this->sourceJourney();
        $treatments = $this->treatments();
        $guestReviewImage = $spaSettings?->guest_review_image;

        if (blank($guestReviewImage) || str_ends_with((string) $guestReviewImage, self::DEFAULT_PHILOSOPHY_IMAGE)) {
            $guestReviewImage = $this->mainStorageUrl(self::DEFAULT_GUEST_REVIEW_IMAGE);
        } else {
            $guestReviewImage = $this->resolveImage($guestReviewImage);
        }

        return view('pages.spa-landing.index', [
            'spaSettings' => $spaSettings,
            'heroImage' => $this->resolveImage($spaSettings?->hero_image) ?? $this->mainStorageUrl(self::DEFAULT_HERO_IMAGE),
            'heroMobileImage' => $this->resolveImage($spaSettings?->hero_mobile_image ?: $spaSettings?->hero_image) ?? $this->mainStorageUrl(self::DEFAULT_HERO_IMAGE),
            'wellnessPhilosophyImage' => $this->resolveImage($spaSettings?->wellness_philosophy_image) ?? $this->mainStorageUrl(self::DEFAULT_PHILOSOPHY_IMAGE),
            'signatureImage' => $this->resolveImage($spaSettings?->signature_image) ?? $this->mainStorageUrl(self::DEFAULT_RIVERSIDE_IMAGE),
            'guestReviewImage' => $guestReviewImage,
            'bookingCtaImage' => $this->resolveImage($spaSettings?->booking_cta_image) ?? $this->mainStorageUrl(self::DEFAULT_RIVERSIDE_IMAGE),
            'sourceJourney' => $sourceJourney,
            'treatments' => $treatments,
        ]);
    }

    /**
     * Use the active voucher catalogue as the authoritative treatment source.
     */
    private function treatments(): Collection
    {
        if (! Schema::hasTable('vouchers')) {
            return collect();
        }

        return Voucher::query()
            ->active()
            ->where('voucher_type', 'spa')
            ->with('category')
            ->ordered()
            ->limit(6)
            ->get();
    }

    /**
     * Use the published spa package as the single source of truth for this card.
     *
     * @return array<string, string|null>|null
     */
    private function sourceJourney(): ?array
    {
        if (! Schema::hasTable('spas')) {
            return null;
        }

        $spa = Spa::query()
            ->published()
            ->where('slug', self::MYSTICAL_JOURNEY_SLUG)
            ->first();

        if (! $spa) {
            return null;
        }

        $description = html_entity_decode(strip_tags((string) ($spa->excerpt ?: $spa->description)), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $description = trim((string) preg_replace('/\s+/', ' ', $description));

        return [
            'title' => $spa->title,
            'description' => $description,
            'image' => $spa->card_image ?: $spa->hero_image,
            'image_alt' => $spa->card_image_alt ?: $spa->hero_image_alt ?: $spa->title,
            'details_label' => 'View Details',
            'details_url' => route('spa-landing.treatments.show', $spa->slug),
            'book_label' => $spa->button_label ?: 'Book Now',
            'book_url' => self::MYSTICAL_JOURNEY_BOOKING_URL,
        ];
    }

    private function resolveImage(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, '/')) {
            return asset($path);
        }

        return Storage::disk('public')->url($path);
    }

    private function mainStorageUrl(string $path): string
    {
        return 'https://'.config('domains.main').'/storage/'.ltrim($path, '/');
    }
}
