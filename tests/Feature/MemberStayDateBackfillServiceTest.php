<?php

namespace Tests\Feature;

use App\Models\Member;
use App\Models\SyncedWebhotelierBooking;
use App\Services\MemberStayDateBackfillService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MemberStayDateBackfillServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_fills_blank_member_stay_dates_from_latest_synced_booking(): void
    {
        $member = Member::create([
            'name' => 'Backfill Guest',
            'email' => 'backfill@example.com',
        ]);

        SyncedWebhotelierBooking::create([
            'member_id' => $member->id,
            'booking_number' => 'OLD-1001',
            'email' => $member->email,
            'check_in' => '2026-07-02',
            'check_out' => '2026-07-04',
        ]);

        SyncedWebhotelierBooking::create([
            'member_id' => $member->id,
            'booking_number' => 'NEW-1001',
            'email' => $member->email,
            'check_in' => '2026-07-10',
            'check_out' => '2026-07-12',
        ]);

        $summary = app(MemberStayDateBackfillService::class)->backfill();

        $member->refresh();

        $this->assertSame(['checked' => 1, 'updated' => 1, 'skipped' => 0], $summary);
        $this->assertSame('2026-07-10', $member->booking_check_in->toDateString());
        $this->assertSame('2026-07-12', $member->booking_check_out->toDateString());
    }

    public function test_it_does_not_overwrite_existing_member_stay_dates(): void
    {
        $member = Member::create([
            'name' => 'Manual Guest',
            'email' => 'manual@example.com',
            'booking_check_in' => '2026-07-20',
            'booking_check_out' => '2026-07-25',
        ]);

        SyncedWebhotelierBooking::create([
            'member_id' => $member->id,
            'booking_number' => 'SYNC-1001',
            'email' => $member->email,
            'check_in' => '2026-07-02',
            'check_out' => '2026-07-04',
        ]);

        $summary = app(MemberStayDateBackfillService::class)->backfill();

        $member->refresh();

        $this->assertSame(['checked' => 0, 'updated' => 0, 'skipped' => 0], $summary);
        $this->assertSame('2026-07-20', $member->booking_check_in->toDateString());
        $this->assertSame('2026-07-25', $member->booking_check_out->toDateString());
    }
}
