<?php

namespace App\Http\Controllers\Cron;

use App\Http\Controllers\Controller;
use App\Services\OfferPublicationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class OfferPublicationController extends Controller
{
    public function __invoke(string $token, OfferPublicationService $service): JsonResponse
    {
        $expectedToken = (string) config('services.offers.publication_cron_token');

        if ($expectedToken === '' || ! hash_equals($expectedToken, $token)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid cron token.',
            ], 403);
        }

        $lock = Cache::lock('offer-publication-cron', 300);

        if (! $lock->get()) {
            return response()->json([
                'success' => false,
                'message' => 'Offer publication cron is already running.',
            ], 429);
        }

        try {
            $summary = $service->sync();

            return response()->json([
                'success' => true,
                'message' => 'Offer publication cron completed.',
                ...$summary,
            ]);
        } finally {
            optional($lock)->release();
        }
    }
}
