<?php

namespace App\Services;

use App\Models\Member;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MembershipLifecycleService
{
    public function sendExpiryReminders(int $daysBeforeExpiry = 90): int
    {
        $targetDate = Carbon::today()->addDays($daysBeforeExpiry)->toDateString();
        $sent = 0;

        Member::query()
            ->whereNotNull('membership_expires_at')
            ->whereDate('membership_expires_at', $targetDate)
            ->whereNull('membership_expiry_reminder_sent_at')
            ->orderBy('id')
            ->chunkById(100, function ($members) use (&$sent): void {
                foreach ($members as $member) {
                    if (blank($member->email)) {
                        continue;
                    }

                    try {
                        // Lifecycle emails are rendered locally and delivered by the membership relay.
                        $result = app(MembershipEmailRelayService::class)->sendView('emails.membership.expiry-reminder', [
                            'member' => $member,
                            'dashboardUrl' => route('membership.dashboard'),
                        ], [
                            'to' => $member->email,
                            'bcc' => $this->guestBcc(),
                            'subject' => 'Your Membership Tier Is About to Be Downgraded',
                        ]);

                        if (! $result['success']) {
                            Log::warning('Membership expiry reminder email could not be sent through relay.', [
                                'member_id' => $member->id,
                                'email' => $member->email,
                                'relay_response' => $result,
                            ]);

                            continue;
                        }

                        $member->forceFill([
                            'membership_expiry_reminder_sent_at' => now(),
                        ])->save();

                        $sent++;
                    } catch (\Throwable $e) {
                        Log::warning('Membership expiry reminder email could not be sent.', [
                            'member_id' => $member->id,
                            'email' => $member->email,
                            'message' => $e->getMessage(),
                        ]);
                    }
                }
            });

        return $sent;
    }

    public function processExpiredMemberships(): array
    {
        $summary = [
            'renewed' => 0,
            'downgraded' => 0,
            'skipped' => 0,
        ];

        Member::query()
            ->whereNotNull('membership_expires_at')
            ->where('membership_expires_at', '<=', now())
            ->orderBy('id')
            ->chunkById(100, function ($members) use (&$summary): void {
                foreach ($members as $member) {
                    $result = $this->processExpiredMember($member);
                    $summary[$result]++;
                }
            });

        return $summary;
    }

    public function processExpiredMember(Member $member): string
    {
        if (! $member->membership_expires_at || $member->membership_expires_at->isFuture()) {
            return 'skipped';
        }

        return $this->hasEarnedPointsDuringMembershipYear($member)
            ? $this->renewMember($member)
            : $this->downgradeMember($member);
    }

    private function hasEarnedPointsDuringMembershipYear(Member $member): bool
    {
        $query = $member->pointTransactions()
            ->where('type', Member::POINT_TYPE_EARN)
            ->where('points', '>', 0);

        if ($member->membership_started_at) {
            $query->where('created_at', '>=', $member->membership_started_at);
        }

        if ($member->membership_expires_at) {
            $query->where('created_at', '<=', $member->membership_expires_at);
        }

        return $query->exists();
    }

    private function renewMember(Member $member): string
    {
        $member->forceFill([
            'membership_started_at' => now(),
            'membership_expires_at' => now()->addYear(),
            'membership_expiry_reminder_sent_at' => null,
        ])->save();

        return 'renewed';
    }

    private function downgradeMember(Member $member): string
    {
        $previousTier = (string) ($member->tier ?? Member::TIER_BRONZE);
        $newTier = Member::getDowngradedTier($previousTier);
        $previousPoints = (int) $member->points;
        $newTierMaximumPoints = Member::getMaximumPointsForTier($newTier);
        $newPoints = $newTierMaximumPoints === null
            ? $previousPoints
            : min($previousPoints, $newTierMaximumPoints);

        DB::transaction(function () use ($member, $newTier, $previousPoints, $newPoints): void {
            $member->forceFill([
                'tier' => $newTier,
                'points' => $newPoints,
                'membership_started_at' => now(),
                'membership_expires_at' => now()->addYear(),
                'last_tier_downgraded_at' => now(),
                'membership_expiry_reminder_sent_at' => null,
            ])->save();

            $pointsAdjustment = $newPoints - $previousPoints;

            if ($pointsAdjustment !== 0) {
                $member->pointTransactions()->create([
                    'type' => Member::POINT_TYPE_ADJUSTMENT,
                    'points' => $pointsAdjustment,
                    'description' => 'Yearly tier downgrade point adjustment',
                    'reference_type' => 'membership_downgrade',
                    'reference_id' => $member->id,
                ]);
            }
        });

        try {
            $result = app(MembershipEmailRelayService::class)->sendView('emails.membership.tier-downgraded', [
                'member' => $member,
                'previousTierLabel' => Member::getTierLabelForTier($previousTier),
                'newTierLabel' => Member::getTierLabelForTier($newTier),
                'previousPoints' => $previousPoints,
                'newPoints' => $newPoints,
                'dashboardUrl' => route('membership.dashboard'),
            ], [
                'to' => $member->email,
                'bcc' => $this->guestBcc(),
                'subject' => 'Your Membership Tier Has Been Updated',
            ]);

            if (! $result['success']) {
                Log::warning('Member tier downgrade email could not be sent through relay.', [
                    'member_id' => $member->id,
                    'email' => $member->email,
                    'previous_tier' => $previousTier,
                    'new_tier' => $newTier,
                    'relay_response' => $result,
                ]);
            }
        } catch (\Throwable $e) {
            Log::warning('Member tier downgrade email could not be sent.', [
                'member_id' => $member->id,
                'email' => $member->email,
                'previous_tier' => $previousTier,
                'new_tier' => $newTier,
                'message' => $e->getMessage(),
            ]);
        }

        return 'downgraded';
    }

    /**
     * @return array<int, string>
     */
    private function guestBcc(): array
    {
        $bcc = trim((string) config('mail.guest_bcc'));

        return $bcc === '' ? [] : [$bcc];
    }
}
