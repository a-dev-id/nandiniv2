<?php

namespace App\Http\Controllers\Cron;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Services\MembershipEmailRelayService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TestWelcomeEmailController extends Controller
{
    public function __invoke(string $token, Request $request): JsonResponse
    {
        $expectedToken = (string) config('services.welcome_email.test_token');

        if ($expectedToken === '' || ! hash_equals($expectedToken, $token)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid test token.',
            ], 403);
        }

        $email = trim((string) $request->query('email'));

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return response()->json([
                'success' => false,
                'message' => 'A valid email query parameter is required.',
            ], 422);
        }

        $member = new Member([
            'first_name' => 'Test',
            'last_name' => 'Member',
            'name' => 'Test Member',
            'email' => $email,
            'member_source' => Member::SOURCE_AUTO_JOIN,
            'tier' => Member::TIER_BRONZE,
            'points' => 0,
        ]);

        // This endpoint tests the real relay-backed welcome email path.
        $result = app(MembershipEmailRelayService::class)->sendView('emails.membership.auto-join-welcome', [
            'member' => $member,
            'bookingNumber' => 'TEST-BOOKING',
            'roomName' => 'Jungle View Villa',
            'checkinDate' => now()->toDateString(),
            'checkoutDate' => now()->addDay()->toDateString(),
            'loginUrl' => route('membership.login'),
            'passwordResetUrl' => route('membership.password.request'),
        ], [
            'to' => $email,
            'bcc' => $this->guestBcc(),
            'subject' => 'Welcome to Nandini Inner Circle',
        ]);

        if (! $result['success']) {
            return response()->json([
                'success' => false,
                'message' => 'Test welcome email could not be sent.',
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Test welcome email sent.',
        ]);
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
