<?php

namespace Tests\Feature;

use App\Models\WebhotelierReservation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingSyncFeedTest extends TestCase
{
    use RefreshDatabase;

    public function test_booking_sync_feed_requires_bearer_token(): void
    {
        config(['services.membership_api.token' => 'sync-token']);

        $this->getJson('/api/bookings/sync')
            ->assertUnauthorized()
            ->assertJson([
                'success' => false,
                'message' => 'Unauthorized.',
            ]);
    }

    public function test_booking_sync_feed_returns_webhotelier_reservations_since_requested_time(): void
    {
        config(['services.membership_api.token' => 'sync-token']);

        $oldReservation = WebhotelierReservation::create([
            'webhotelier_id' => 'OLD-1001',
            'guest_email' => 'old@example.com',
            'last_received_at' => '2026-07-02 20:00:00',
        ]);
        $oldReservation->forceFill([
            'created_at' => '2026-07-02 20:00:00',
            'updated_at' => '2026-07-02 20:00:00',
        ])->save();

        $newReservation = WebhotelierReservation::create([
            'webhotelier_id' => '61553777',
            'guest_email' => 'jil.schanen@gmail.com',
            'guest_first_name' => 'Jil',
            'guest_last_name' => 'Schanen',
            'checkin_date' => '2026-09-18',
            'checkout_date' => '2026-09-21',
            'rooms' => 1,
            'room_name' => 'Panoramic Corner Jacuzzi Royal Suite',
            'currency' => 'IDR',
            'booking_total' => 10949805,
            'status_code' => 'CONFIRMED',
            'last_received_at' => '2026-07-03 02:07:03',
        ]);
        $newReservation->forceFill([
            'created_at' => '2026-07-03 02:07:03',
            'updated_at' => '2026-07-03 02:07:03',
        ])->save();

        $this->withToken('sync-token')
            ->getJson('/api/bookings/sync?since=2026-07-03%2000:00:00')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonCount(1, 'bookings')
            ->assertJsonPath('bookings.0.booking_number', '61553777')
            ->assertJsonPath('bookings.0.guest_name', 'Jil Schanen')
            ->assertJsonPath('bookings.0.email', 'jil.schanen@gmail.com')
            ->assertJsonPath('bookings.0.check_in', '2026-09-18')
            ->assertJsonPath('bookings.0.check_out', '2026-09-21')
            ->assertJsonPath('bookings.0.status', 'CONFIRMED');
    }
}
