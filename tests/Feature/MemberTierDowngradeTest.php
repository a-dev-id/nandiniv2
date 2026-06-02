<?php

namespace Tests\Feature;

use App\Models\Member;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MemberTierDowngradeTest extends TestCase
{
    use RefreshDatabase;

    public function test_yearly_downgrade_steps_down_one_tier_and_keeps_remaining_points_when_inside_new_cap(): void
    {
        $member = Member::create([
            'name' => 'Test Member',
            'email' => 'member@example.com',
            'tier' => Member::TIER_PLATINUM,
            'points' => 700,
            'membership_started_at' => now()->subYear(),
            'membership_expires_at' => now()->subDay(),
        ]);

        $this->assertTrue($member->applyYearlyTierDowngrade());

        $member->refresh();

        $this->assertSame(Member::TIER_GOLD, $member->tier);
        $this->assertSame(700, $member->points);
        $this->assertDatabaseCount('member_point_transactions', 0);
    }

    public function test_yearly_downgrade_caps_points_to_the_new_tier_maximum(): void
    {
        $member = Member::create([
            'name' => 'Test Member',
            'email' => 'member@example.com',
            'tier' => Member::TIER_SILVER,
            'points' => 700,
            'membership_started_at' => now()->subYear(),
            'membership_expires_at' => now()->subDay(),
        ]);

        $this->assertTrue($member->applyYearlyTierDowngrade());

        $member->refresh();

        $this->assertSame(Member::TIER_BRONZE, $member->tier);
        $this->assertSame(400, $member->points);
        $this->assertDatabaseHas('member_point_transactions', [
            'member_id' => $member->id,
            'type' => Member::POINT_TYPE_ADJUSTMENT,
            'points' => -300,
            'description' => 'Yearly tier downgrade point adjustment',
        ]);
    }
}
