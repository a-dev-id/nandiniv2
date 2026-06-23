<?php

namespace App\Http\Controllers\Cron;

use App\Http\Controllers\Controller;
use App\Mail\AutoJoinWelcomeMail;
use App\Models\Member;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

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

        Mail::to($email)->send(new AutoJoinWelcomeMail(
            member: $member,
            bookingNumber: 'TEST-BOOKING',
            roomName: 'Jungle View Villa',
            checkinDate: now()->toDateString(),
            checkoutDate: now()->addDay()->toDateString(),
        ));

        return response()->json([
            'success' => true,
            'message' => 'Test welcome email sent.',
        ]);
    }
}
