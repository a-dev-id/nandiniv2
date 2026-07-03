<?php

namespace Tests\Feature;

use App\Models\BookingSyncLog;
use App\Services\BookingSyncService;
use App\Services\MembershipBookingApiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingSyncLookbackTest extends TestCase
{
    use RefreshDatabase;

    public function test_automatic_booking_sync_uses_overlap_before_last_successful_sync(): void
    {
        BookingSyncLog::create([
            'started_at' => '2026-07-03 09:00:00',
            'finished_at' => '2026-07-03 09:30:00',
            'status' => BookingSyncLog::STATUS_SUCCESS,
            'message' => 'Previous sync completed.',
        ]);

        $api = $this->mock(MembershipBookingApiService::class, function ($mock): void {
            $mock->shouldReceive('fetchBookings')
                ->once()
                ->with('2026-07-01 09:30:00')
                ->andReturn([]);

            $mock->shouldReceive('debugData')->andReturn([]);
        });

        $summary = app(BookingSyncService::class, ['api' => $api])->sync();

        $this->assertTrue($summary['success']);
        $this->assertSame('2026-07-01 09:30:00', $summary['since_used']);
    }

    public function test_manual_booking_sync_since_override_is_used_exactly(): void
    {
        BookingSyncLog::create([
            'started_at' => '2026-07-03 09:00:00',
            'finished_at' => '2026-07-03 09:30:00',
            'status' => BookingSyncLog::STATUS_SUCCESS,
            'message' => 'Previous sync completed.',
        ]);

        $api = $this->mock(MembershipBookingApiService::class, function ($mock): void {
            $mock->shouldReceive('fetchBookings')
                ->once()
                ->with('2026-07-02 20:00:00')
                ->andReturn([]);

            $mock->shouldReceive('debugData')->andReturn([]);
        });

        $summary = app(BookingSyncService::class, ['api' => $api])->sync('2026-07-02 20:00:00');

        $this->assertTrue($summary['success']);
        $this->assertSame('2026-07-02 20:00:00', $summary['since_used']);
    }
}
