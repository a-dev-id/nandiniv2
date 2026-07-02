<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\MemberRewardRedemption;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\URL;
use Illuminate\View\View;

class MemberEmailPreviewController extends Controller
{
    public function __invoke(string $token, ?string $template = null): Response|View
    {
        $expectedToken = (string) config('services.mail_test_token');

        if ($expectedToken === '' || ! hash_equals($expectedToken, $token)) {
            abort(403, 'Invalid mail preview token.');
        }

        $previews = $this->previews();

        if ($template === null) {
            return response($this->indexHtml($token, $previews));
        }

        if (! array_key_exists($template, $previews)) {
            abort(404, 'Email preview not found.');
        }

        $preview = $previews[$template];

        return view($preview['view'], $preview['data']);
    }

    private function previews(): array
    {
        $member = new Member([
            'first_name' => 'Angga',
            'last_name' => 'Gardens',
            'name' => 'Angga Gardens',
            'email' => 'member@example.com',
            'tier' => Member::TIER_BRONZE,
            'points' => 125,
            'member_source' => Member::SOURCE_AUTO_JOIN,
        ]);

        $member->id = 1;

        $redemption = new MemberRewardRedemption([
            'reward_name' => 'Jungle Spa Experience',
            'points_used' => 250,
            'status' => MemberRewardRedemption::STATUS_PENDING,
            'redemption_code' => 'RDM-20260702-PREVIEW',
            'expires_at' => now()->addMonth(),
        ]);
        $redemption->id = 1;
        $redemption->created_at = now();

        $usedRedemption = new MemberRewardRedemption([
            'reward_name' => 'Jungle Spa Experience',
            'points_used' => 250,
            'status' => MemberRewardRedemption::STATUS_USED,
            'redemption_code' => 'RDM-20260702-SUCCESS',
            'expires_at' => now()->addMonth(),
            'used_at' => now(),
        ]);
        $usedRedemption->id = 2;
        $usedRedemption->created_at = now()->subDay();

        return [
            'verify-email' => [
                'title' => 'Verify Email',
                'subject' => 'Verify Your Nandini Inner Circle Email',
                'view' => 'emails.membership.verify-email',
                'data' => [
                    'member' => $member,
                    'verificationUrl' => URL::temporarySignedRoute(
                        'membership.verify.email',
                        now()->addHours(24),
                        [
                            'member' => $member->id,
                            'hash' => sha1($member->email),
                        ]
                    ),
                ],
            ],
            'reset-password' => [
                'title' => 'Reset Password',
                'subject' => 'Reset Your Nandini Inner Circle Password',
                'view' => 'emails.membership.reset-password',
                'data' => [
                    'member' => $member,
                    'resetUrl' => route('membership.password.reset', [
                        'token' => 'preview-reset-token',
                        'email' => $member->email,
                    ]),
                    'expiresInMinutes' => config('auth.passwords.members.expire', 60),
                ],
            ],
            'auto-join-welcome' => [
                'title' => 'Auto Join Welcome',
                'subject' => 'Welcome to Nandini Inner Circle',
                'view' => 'emails.membership.auto-join-welcome',
                'data' => [
                    'member' => $member,
                    'bookingNumber' => 'WH-2026-PREVIEW',
                    'roomName' => 'Jungle View Villa',
                    'checkinDate' => now()->addWeeks(2)->format('Y-m-d'),
                    'checkoutDate' => now()->addWeeks(2)->addDays(2)->format('Y-m-d'),
                    'loginUrl' => route('membership.login'),
                    'passwordResetUrl' => route('membership.password.request'),
                ],
            ],
            'expiry-reminder' => [
                'title' => 'Membership Expiry Reminder',
                'subject' => 'Your Membership Tier Is About to Be Downgraded',
                'view' => 'emails.membership.expiry-reminder',
                'data' => [
                    'member' => tap(clone $member, function (Member $member): void {
                        $member->membership_expires_at = now()->addDays(90);
                    }),
                    'dashboardUrl' => route('membership.dashboard'),
                ],
            ],
            'points-added' => [
                'title' => 'Points Added',
                'subject' => 'Your Points Have Been Added to Your Account',
                'view' => 'emails.membership.points-added',
                'data' => [
                    'member' => tap(clone $member, function (Member $member): void {
                        $member->points = 800;
                        $member->tier = Member::TIER_SILVER;
                    }),
                    'previousTierLabel' => Member::getTierLabelForTier(Member::TIER_BRONZE),
                    'previousPoints' => 300,
                    'pointsAdded' => 500,
                    'newTierLabel' => Member::getTierLabelForTier(Member::TIER_SILVER),
                    'totalPoints' => 800,
                    'description' => null,
                    'dashboardUrl' => route('membership.dashboard'),
                ],
            ],
            'reward-redeemed' => [
                'title' => 'Reward Redeemed',
                'subject' => 'Your Reward Redemption Confirmation',
                'view' => 'emails.membership.reward-redeemed',
                'data' => [
                    'member' => $member,
                    'redemption' => $redemption,
                    'redeemDate' => now()->addWeek()->format('d F Y'),
                    'redeemTime' => '14:00',
                    'specialRequest' => 'Please prepare a quiet table near the jungle view.',
                    'thankYouUrl' => route('membership.rewards.thank-you', $redemption),
                ],
            ],
            'reward-redemption-success' => [
                'title' => 'Reward Redemption Success',
                'subject' => 'Your Reward Has Been Successfully Redeemed',
                'view' => 'emails.membership.reward-redemption-success',
                'data' => [
                    'member' => $member,
                    'redemption' => $usedRedemption,
                    'dashboardUrl' => route('membership.dashboard'),
                ],
            ],
            'tier-downgraded' => [
                'title' => 'Tier Downgraded',
                'subject' => 'Your Membership Tier Has Been Updated',
                'view' => 'emails.membership.tier-downgraded',
                'data' => [
                    'member' => tap(clone $member, function (Member $member): void {
                        $member->tier = Member::TIER_BRONZE;
                        $member->points = 400;
                        $member->membership_expires_at = now()->addYear();
                    }),
                    'previousTierLabel' => Member::getTierLabelForTier(Member::TIER_SILVER),
                    'newTierLabel' => Member::getTierLabelForTier(Member::TIER_BRONZE),
                    'previousPoints' => 500,
                    'newPoints' => 400,
                    'dashboardUrl' => route('membership.dashboard'),
                ],
            ],
        ];
    }

    private function indexHtml(string $token, array $previews): string
    {
        $items = collect($previews)
            ->map(function (array $preview, string $slug) use ($token): string {
                $url = route('member-email-preview.show', [
                    'token' => $token,
                    'template' => $slug,
                ]);

                return sprintf(
                    '<li><a href="%s" target="_blank" rel="noopener">%s</a><span>%s</span></li>',
                    e($url),
                    e($preview['title']),
                    e($preview['subject'])
                );
            })
            ->implode('');

        return '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nandini Member Email Previews</title>
    <style>
        body{margin:0;background:#f6f3ee;color:#172033;font-family:Arial,Helvetica,sans-serif}
        main{max-width:860px;margin:0 auto;padding:48px 22px}
        h1{font-family:Georgia,serif;font-size:30px;letter-spacing:3px;text-transform:uppercase;font-weight:400;margin:0 0 12px}
        p{color:#556070;line-height:1.7;margin:0 0 28px}
        ul{list-style:none;margin:0;padding:0;border:1px solid #e5ddcf;background:#fff}
        li{display:flex;gap:18px;justify-content:space-between;align-items:center;padding:18px 20px;border-bottom:1px solid #eee8df}
        li:last-child{border-bottom:0}
        a{color:#916b2c;text-transform:uppercase;letter-spacing:2px;font-size:13px;font-weight:700;text-decoration:none}
        a:hover{text-decoration:underline}
        span{color:#667085;font-size:13px;text-align:right}
        @media (max-width:640px){li{display:block}span{display:block;text-align:left;margin-top:8px}}
    </style>
</head>
<body>
    <main>
        <h1>Member Email Previews</h1>
        <p>These previews use sample member data and do not send any email.</p>
        <ul>' . $items . '</ul>
    </main>
</body>
</html>';
    }
}
