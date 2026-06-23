<?php

namespace Tests\Feature;

use App\Models\Member;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class MemberPointValidityTest extends TestCase
{
    use RefreshDatabase;

    public function test_earning_points_extends_active_membership_from_current_expiry(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-06-13 10:00:00'));

        $member = Member::create([
            'name' => 'Active Member',
            'email' => 'active-member@example.com',
            'tier' => Member::TIER_BRONZE,
            'points' => 100,
            'membership_started_at' => now()->subMonth(),
            'membership_expires_at' => now()->addMonth(),
            'membership_expiry_reminder_sent_at' => now()->subDay(),
        ]);

        $member->earnPoints(50, 'Test points');

        $member->refresh();

        $this->assertSame(150, $member->points);
        $this->assertTrue($member->membership_started_at->equalTo(now()->subMonth()));
        $this->assertTrue($member->membership_expires_at->equalTo(now()->addMonth()->addYear()));
        $this->assertNull($member->membership_expiry_reminder_sent_at);

        Carbon::setTestNow();
    }

    public function test_earning_points_reactivates_expired_membership_for_one_year_from_today(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-06-13 10:00:00'));

        $member = Member::create([
            'name' => 'Expired Member',
            'email' => 'expired-member@example.com',
            'tier' => Member::TIER_BRONZE,
            'points' => 100,
            'membership_started_at' => now()->subYears(2),
            'membership_expires_at' => now()->subMonth(),
            'membership_expiry_reminder_sent_at' => now()->subDay(),
        ]);

        $member->earnPoints(50, 'Test points');

        $member->refresh();

        $this->assertSame(150, $member->points);
        $this->assertTrue($member->membership_started_at->equalTo(now()));
        $this->assertTrue($member->membership_expires_at->equalTo(now()->addYear()));
        $this->assertNull($member->membership_expiry_reminder_sent_at);

        Carbon::setTestNow();
    }

    public function test_redeeming_points_does_not_extend_membership_validity(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-06-13 10:00:00'));

        $member = Member::create([
            'name' => 'Redeeming Member',
            'email' => 'redeeming-member@example.com',
            'tier' => Member::TIER_BRONZE,
            'points' => 100,
            'membership_started_at' => now()->subMonth(),
            'membership_expires_at' => now()->addMonth(),
        ]);

        $member->redeemPoints(50, 'Test redemption');

        $member->refresh();

        $this->assertSame(50, $member->points);
        $this->assertTrue($member->membership_started_at->equalTo(now()->subMonth()));
        $this->assertTrue($member->membership_expires_at->equalTo(now()->addMonth()));

        Carbon::setTestNow();
    }
}
