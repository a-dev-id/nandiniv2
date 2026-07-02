<?php

namespace App\Http\Controllers\Cron;

use App\Http\Controllers\Controller;
use App\Services\MemberCheckoutNotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class MemberCheckoutNotificationController extends Controller
{
    public function __invoke(string $token, MemberCheckoutNotificationService $service, Request $request): JsonResponse
    {
        $expectedToken = (string) config('services.membership.lifecycle_cron_token');

        if ($expectedToken === '' || ! hash_equals($expectedToken, $token)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid cron token.',
            ], 403);
        }

        $lock = Cache::lock('member-checkout-notification-cron', 600);

        if (! $lock->get()) {
            return response()->json([
                'success' => false,
                'message' => 'Member checkout notification cron is already running.',
            ], 429);
        }

        try {
            $summary = $service->sendTodayNotifications($request->query('date'));

            return response()->json([
                'success' => $summary['failed'] === 0,
                'message' => 'Member checkout notification cron completed.',
                'checkout_date' => $summary['date'],
                'notifications_sent' => $summary['sent'],
                'notifications_failed' => $summary['failed'],
                'notifications_already_sent' => $summary['skipped'],
            ], $summary['failed'] === 0 ? 200 : 500);
        } finally {
            optional($lock)->release();
        }
    }
}
