<?php

namespace Tests\Feature;

use App\Models\Member;
use App\Models\SyncedWebhotelierBooking;
use App\Services\BookingSyncService;
use App\Services\MemberAutoJoinService;
use App\Services\MembershipBookingApiService;
use App\Services\MembershipEmailRelayService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MemberAutoJoinCutoffTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_does_not_auto_join_booking_created_before_july_first_even_if_cancelled_after_cutoff(): void
    {
        $result = app(MemberAutoJoinService::class)->autoJoinFromWebhotelierReservation('BK-1001', [
            'created_at' => '2026-06-01 10:00:00',
            'remote_updated_at' => '2026-07-02 10:00:00',
            'statusCode' => 'cancelled',
            'clientInfo' => [
                'email' => 'old-booking@example.com',
                'firstName' => 'Old',
                'lastName' => 'Booking',
            ],
        ]);

        $this->assertFalse($result['created']);
        $this->assertTrue($result['skipped']);
        $this->assertDatabaseMissing('members', [
            'email' => 'old-booking@example.com',
        ]);
    }

    public function test_it_does_not_auto_join_booking_created_on_july_first(): void
    {
        $result = app(MemberAutoJoinService::class)->autoJoinFromWebhotelierReservation('BK-1002', [
            'created_at' => '2026-07-01 23:59:59',
            'statusCode' => 'confirmed',
            'clientInfo' => [
                'email' => 'july-first@example.com',
                'firstName' => 'July',
                'lastName' => 'First',
            ],
        ]);

        $this->assertFalse($result['created']);
        $this->assertTrue($result['skipped']);
        $this->assertDatabaseMissing('members', [
            'email' => 'july-first@example.com',
        ]);
    }

    public function test_it_auto_joins_booking_created_after_july_first(): void
    {
        $this->mock(MembershipEmailRelayService::class, function ($mock): void {
            $mock->shouldReceive('sendView')->once()->andReturn([
                'success' => true,
                'error' => null,
            ]);
        });

        $result = app(MemberAutoJoinService::class)->autoJoinFromWebhotelierReservation('BK-1003', [
            'created_at' => '2026-07-02 00:00:00',
            'statusCode' => 'confirmed',
            'clientInfo' => [
                'email' => 'new-booking@example.com',
                'firstName' => 'New',
                'lastName' => 'Booking',
            ],
        ]);

        $this->assertTrue($result['created']);
        $this->assertFalse($result['skipped']);
        $this->assertDatabaseHas('members', [
            'email' => 'new-booking@example.com',
            'member_source' => Member::SOURCE_AUTO_JOIN,
        ]);
    }

    public function test_booking_sync_does_not_create_member_for_booking_created_before_cutoff(): void
    {
        $api = $this->mock(MembershipBookingApiService::class, function ($mock): void {
            $mock->shouldReceive('fetchBookings')->once()->andReturn([
                [
                    'booking_number' => 'SYNC-1001',
                    'created_at' => '2026-06-01 10:00:00',
                    'remote_updated_at' => '2026-07-02 10:00:00',
                    'status' => 'cancelled',
                    'email' => 'old-sync-booking@example.com',
                    'guest_name' => 'Old Sync Booking',
                ],
            ]);
            $mock->shouldReceive('debugData')->andReturn([]);
        });

        $summary = app(BookingSyncService::class, ['api' => $api])->sync('2026-07-02 00:00:00');

        $this->assertTrue($summary['success']);
        $this->assertSame(0, $summary['members_created']);
        $this->assertDatabaseMissing('members', [
            'email' => 'old-sync-booking@example.com',
        ]);
        $this->assertNull(SyncedWebhotelierBooking::where('booking_number', 'SYNC-1001')->value('member_id'));
    }

    public function test_booking_sync_creates_member_for_booking_created_after_cutoff(): void
    {
        $api = $this->mock(MembershipBookingApiService::class, function ($mock): void {
            $mock->shouldReceive('fetchBookings')->once()->andReturn([
                [
                    'booking_number' => 'SYNC-1002',
                    'created_at' => '2026-07-02 00:00:00',
                    'status' => 'confirmed',
                    'email' => 'new-sync-booking@example.com',
                    'guest_name' => 'New Sync Booking',
                ],
            ]);
            $mock->shouldReceive('debugData')->andReturn([]);
        });

        $this->mock(MembershipEmailRelayService::class, function ($mock): void {
            $mock->shouldReceive('sendView')->once()->andReturn([
                'success' => true,
                'error' => null,
            ]);
        });

        $summary = app(BookingSyncService::class, ['api' => $api])->sync('2026-07-02 00:00:00');

        $this->assertTrue($summary['success']);
        $this->assertSame(1, $summary['members_created']);
        $this->assertDatabaseHas('members', [
            'email' => 'new-sync-booking@example.com',
            'member_source' => Member::SOURCE_AUTO_JOIN,
        ]);
        $this->assertNotNull(SyncedWebhotelierBooking::where('booking_number', 'SYNC-1002')->value('member_id'));
    }
}
