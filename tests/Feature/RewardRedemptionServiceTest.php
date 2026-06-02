<?php

namespace Tests\Feature;

use App\Models\Member;
use App\Models\MemberRewardRedemption;
use App\Models\Reward;
use App\Services\RewardRedemptionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Tests\TestCase;

class RewardRedemptionServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_deducts_points_and_creates_a_pending_redemption(): void
    {
        $member = Member::create([
            'name' => 'Test Member',
            'email' => 'member@example.com',
            'points' => 600,
        ]);

        $reward = Reward::create([
            'title' => 'Spa Reward',
            'slug' => 'spa-reward',
            'points_required' => 250,
            'is_active' => true,
        ]);

        $redemption = app(RewardRedemptionService::class)->redeem($member, $reward);

        $this->assertSame(350, $member->fresh()->points);
        $this->assertSame(MemberRewardRedemption::STATUS_PENDING, $redemption->status);
        $this->assertSame(250, $redemption->points_used);
        $this->assertSame($member->id, $redemption->member_id);
        $this->assertSame($reward->id, $redemption->reward_id);
        $this->assertNotNull($redemption->member_point_transaction_id);

        $this->assertDatabaseHas('member_point_transactions', [
            'member_id' => $member->id,
            'type' => Member::POINT_TYPE_REDEEM,
            'points' => -250,
            'reference_type' => 'rewards',
            'reference_id' => $reward->id,
        ]);
    }

    public function test_redeeming_points_does_not_downgrade_the_members_current_tier(): void
    {
        $member = Member::create([
            'name' => 'Test Member',
            'email' => 'member@example.com',
            'tier' => Member::TIER_PLATINUM,
            'points' => 2000,
        ]);

        $reward = Reward::create([
            'title' => 'Villa Reward',
            'slug' => 'villa-reward',
            'points_required' => 1300,
            'is_active' => true,
        ]);

        app(RewardRedemptionService::class)->redeem($member, $reward);

        $member->refresh();

        $this->assertSame(700, $member->points);
        $this->assertSame(Member::TIER_PLATINUM, $member->tier);
    }

    public function test_it_rejects_redemption_when_member_has_insufficient_points(): void
    {
        $member = Member::create([
            'name' => 'Test Member',
            'email' => 'member@example.com',
            'points' => 100,
        ]);

        $reward = Reward::create([
            'title' => 'Spa Reward',
            'slug' => 'spa-reward',
            'points_required' => 250,
            'is_active' => true,
        ]);

        try {
            app(RewardRedemptionService::class)->redeem($member, $reward);

            $this->fail('Expected the redemption to be rejected.');
        } catch (InvalidArgumentException $exception) {
            $this->assertSame('Member does not have enough points.', $exception->getMessage());
        }

        $this->assertSame(100, $member->fresh()->points);
        $this->assertDatabaseCount('member_reward_redemptions', 0);
        $this->assertDatabaseCount('member_point_transactions', 0);
    }
}
