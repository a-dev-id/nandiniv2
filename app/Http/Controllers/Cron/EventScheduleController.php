<?php

namespace App\Http\Controllers\Cron;

use App\Http\Controllers\Controller;
use App\Services\EventScheduleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class EventScheduleController extends Controller
{
    public function __invoke(string $token, EventScheduleService $service): JsonResponse
    {
        $expectedToken = (string) config('services.events.schedule_cron_token');

        if ($expectedToken === '' || ! hash_equals($expectedToken, $token)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid cron token.',
            ], 403);
        }

        $lock = Cache::lock('event-schedule-cron', 300);

        if (! $lock->get()) {
            return response()->json([
                'success' => false,
                'message' => 'Event schedule cron is already running.',
            ], 429);
        }

        try {
            $summary = $service->sync();

            return response()->json([
                'success' => true,
                'message' => 'Event schedule cron completed.',
                ...$summary,
            ]);
        } finally {
            optional($lock)->release();
        }
    }
}
