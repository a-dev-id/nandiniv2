<?php

namespace App\Http\Controllers;

use App\Models\Spa;
use App\Models\SpaSetting;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SpaLandingController extends Controller
{
    private const MYSTICAL_JOURNEY_SLUG = 'a-mystical-journey-at-nandini-4d3n-wellness-retreat';

    public function __invoke(): View
    {
        $spaSettings = Schema::hasTable('spa_settings') ? SpaSetting::query()->first() : null;
        $sourceJourney = $this->sourceJourney();

        return view('pages.spa-landing.index', [
            'spaSettings' => $spaSettings,
            'heroImage' => $this->resolveImage($spaSettings?->hero_image),
            'heroMobileImage' => $this->resolveImage($spaSettings?->hero_mobile_image ?: $spaSettings?->hero_image),
            'wellnessPhilosophyImage' => $this->resolveImage($spaSettings?->wellness_philosophy_image),
            'signatureImage' => $this->resolveImage($spaSettings?->signature_image),
            'guestReviewImage' => $this->resolveImage($spaSettings?->guest_review_image),
            'bookingCtaImage' => $this->resolveImage($spaSettings?->booking_cta_image),
            'sourceJourney' => $sourceJourney,
        ]);
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
            'details_url' => route('spa.show', $spa->slug),
            'book_label' => $spa->button_label ?: 'Book Now',
            'book_url' => $spa->booking_url_override ?: $spa->button_url,
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
}
