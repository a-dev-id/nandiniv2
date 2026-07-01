<?php

namespace Tests\Feature;

use App\Models\Offer;
use App\Services\OfferPublicationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class OfferPublicationServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_syncs_offer_active_state_from_valid_dates(): void
    {
        Carbon::setTestNow('2026-07-01 09:00:00');

        $futureOffer = $this->createOffer('future-offer', [
            'is_active' => true,
            'valid_start_date' => '2026-07-05',
            'valid_end_date' => '2026-07-20',
        ]);

        $startingOffer = $this->createOffer('starting-offer', [
            'is_active' => false,
            'valid_start_date' => '2026-07-01',
            'valid_end_date' => '2026-07-20',
        ]);

        $expiredOffer = $this->createOffer('expired-offer', [
            'is_active' => true,
            'valid_start_date' => '2026-06-01',
            'valid_end_date' => '2026-06-30',
        ]);

        $manualInactiveOffer = $this->createOffer('manual-inactive-offer', [
            'is_active' => false,
        ]);

        $summary = app(OfferPublicationService::class)->sync();

        $this->assertSame([
            'activated' => 1,
            'deactivated_scheduled' => 1,
            'deactivated_expired' => 1,
        ], $summary);

        $this->assertFalse($futureOffer->fresh()->is_active);
        $this->assertTrue($startingOffer->fresh()->is_active);
        $this->assertFalse($expiredOffer->fresh()->is_active);
        $this->assertFalse($manualInactiveOffer->fresh()->is_active);
    }

    private function createOffer(string $slug, array $attributes = []): Offer
    {
        return Offer::create(array_merge([
            'title' => str($slug)->replace('-', ' ')->title()->toString(),
            'slug' => $slug,
            'is_active' => true,
            'is_featured' => false,
            'sort_order' => 0,
        ], $attributes));
    }
}
