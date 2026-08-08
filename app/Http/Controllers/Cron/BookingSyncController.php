<?php

namespace App\Http\Controllers\Cron;

use App\Http\Controllers\Controller;
use App\Services\BookingSyncService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class BookingSyncController extends Controller
{
    public function __invoke(string $token, BookingSyncService $syncService, Request $request): JsonResponse
    {
        $expectedToken = (string) config('services.membership_api.booking_sync_cron_token');

        if ($expectedToken === '' || ! hash_equals($expectedToken, $token)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid cron token.',
            ], 403);
        }

        $lock = Cache::lock('booking-sync-cron', 600);

        if (! $lock->get()) {
            return response()->json([
                'success' => false,
                'message' => 'Booking sync is already running.',
            ], 429);
        }

        try {
            $summary = $syncService->sync($request->query('since'));

            return response()->json([
                'success' => (bool) $summary['success'],
                'message' => $summary['message'],
                'bookings_received' => $summary['bookings_received'],
                'bookings_created' => $summary['bookings_created'],
                'bookings_updated' => $summary['bookings_updated'],
                'members_created' => $summary['members_created'],
                'members_updated' => $summary['members_updated'],
                'affiliate_bookings' => $summary['affiliate_bookings'],
                'affiliate_booking_warnings' => $summary['affiliate_booking_warnings'],
                'since_used' => $summary['since_used'],
                'membership_api_url_called' => $summary['membership_api_url_called'],
                'membership_api_success' => $summary['membership_api_success'],
                'membership_api_bookings_count' => $summary['membership_api_bookings_count'],
                'membership_api_message' => $summary['membership_api_message'],
            ], $summary['success'] ? 200 : 500);
        } finally {
            optional($lock)->release();
        }
    }
}
