<?php

namespace App\Services;

use App\Models\Member;
use App\Models\MemberRewardRedemption;
use App\Models\Reward;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class RewardRedemptionService
{
    public function redeem(Member $member, Reward $reward, ?string $notes = null): MemberRewardRedemption
    {
        if (! $reward->is_active) {
            throw new InvalidArgumentException('This reward is not active.');
        }

        $pointsRequired = (int) $reward->points_required;

        if ($pointsRequired <= 0) {
            throw new InvalidArgumentException('This reward does not have a valid point requirement.');
        }

        if (! $member->canRedeemPoints($pointsRequired)) {
            throw new InvalidArgumentException('Member does not have enough points.');
        }

        return DB::transaction(function () use ($member, $reward, $pointsRequired, $notes) {
            $member = Member::query()
                ->whereKey($member->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            if (! $member->canRedeemPoints($pointsRequired)) {
                throw new InvalidArgumentException('Member does not have enough points.');
            }

            $pointTransaction = $member->redeemPoints(
                $pointsRequired,
                'Redeemed reward: ' . $reward->title,
                'rewards',
                $reward->id
            );

            return MemberRewardRedemption::create([
                'member_id' => $member->id,
                'reward_id' => $reward->id,
                'member_point_transaction_id' => $pointTransaction->id,
                'reward_name' => $reward->title,
                'points_used' => $pointsRequired,
                'status' => MemberRewardRedemption::STATUS_PENDING,
                'expires_at' => now()->addDays(30),
                'notes' => $notes,
            ]);
        });
    }
}
