<?php

namespace App\Http\Controllers\Cron;

use App\Http\Controllers\Controller;
use App\Services\MembershipLifecycleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class MembershipLifecycleController extends Controller
{
    public function __invoke(string $token, MembershipLifecycleService $service, Request $request): JsonResponse
    {
        $expectedToken = (string) config('services.membership.lifecycle_cron_token');

        if ($expectedToken === '' || ! hash_equals($expectedToken, $token)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid cron token.',
            ], 403);
        }

        $lock = Cache::lock('membership-lifecycle-cron', 600);

        if (! $lock->get()) {
            return response()->json([
                'success' => false,
                'message' => 'Membership lifecycle cron is already running.',
            ], 429);
        }

        try {
            $skipReminders = $request->boolean('skip_reminders');
            $skipExpired = $request->boolean('skip_expired');

            $remindersSent = $skipReminders ? 0 : $service->sendExpiryReminders(90);
            $expiredSummary = $skipExpired
                ? ['renewed' => 0, 'downgraded' => 0, 'skipped' => 0]
                : $service->processExpiredMemberships();

            return response()->json([
                'success' => true,
                'message' => 'Membership lifecycle cron completed.',
                'expiry_reminders_sent' => $remindersSent,
                'expired_memberships_renewed' => $expiredSummary['renewed'],
                'expired_memberships_downgraded' => $expiredSummary['downgraded'],
                'expired_memberships_skipped' => $expiredSummary['skipped'],
            ]);
        } finally {
            optional($lock)->release();
        }
    }
}
