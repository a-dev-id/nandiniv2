<?php

namespace Tests\Feature;

use App\Models\Member;
use App\Models\SyncedWebhotelierBooking;
use App\Services\BookingSyncService;
use App\Services\MembershipBookingApiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingSyncAffiliateCodeTest extends TestCase
{
    use RefreshDatabase;

    public function test_booking_sync_stores_the_api_voucher_code_as_the_affiliate_code(): void
    {
        Member::create([
            'name' => 'Affiliate Guest',
            'email' => 'affiliate-guest@example.com',
        ]);

        $api = $this->mock(MembershipBookingApiService::class, function ($mock): void {
            $mock->shouldReceive('fetchBookings')->once()->andReturn([
                [
                    'booking_number' => 'AFFILIATE-1001',
                    'email' => 'affiliate-guest@example.com',
                    'guest_name' => 'Affiliate Guest',
                    'status' => 'confirmed',
                    'voucher_code' => '  partner86543  ',
                ],
            ]);
            $mock->shouldReceive('debugData')->andReturn([]);
        });

        $summary = app(BookingSyncService::class, ['api' => $api])->sync('2026-08-01 00:00:00');

        $this->assertTrue($summary['success']);
        $this->assertDatabaseHas('synced_webhotelier_bookings', [
            'booking_number' => 'AFFILIATE-1001',
            'affiliate_code' => 'partner86543',
        ]);
    }

    public function test_booking_sync_updates_an_existing_affiliate_code(): void
    {
        $member = Member::create([
            'name' => 'Updated Affiliate Guest',
            'email' => 'updated-affiliate@example.com',
        ]);

        SyncedWebhotelierBooking::create([
            'member_id' => $member->id,
            'booking_number' => 'AFFILIATE-1002',
            'email' => 'updated-affiliate@example.com',
            'affiliate_code' => 'old-code',
        ]);

        $api = $this->mock(MembershipBookingApiService::class, function ($mock): void {
            $mock->shouldReceive('fetchBookings')->once()->andReturn([
                [
                    'booking_number' => 'AFFILIATE-1002',
                    'email' => 'updated-affiliate@example.com',
                    'guest_name' => 'Updated Affiliate Guest',
                    'status' => 'confirmed',
                    'voucher_code' => 'new-code',
                ],
            ]);
            $mock->shouldReceive('debugData')->andReturn([]);
        });

        $summary = app(BookingSyncService::class, ['api' => $api])->sync('2026-08-01 00:00:00');

        $this->assertTrue($summary['success']);
        $this->assertDatabaseHas('synced_webhotelier_bookings', [
            'booking_number' => 'AFFILIATE-1002',
            'affiliate_code' => 'new-code',
        ]);
    }
}
