<?php

namespace App\Http\Controllers\Cron;

use App\Http\Controllers\Controller;
use App\Services\BlogNewsPublicationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class BlogNewsPublicationController extends Controller
{
    public function __invoke(string $token, BlogNewsPublicationService $service): JsonResponse
    {
        $expectedToken = (string) config('services.blog_news.publication_cron_token');

        if ($expectedToken === '' || ! hash_equals($expectedToken, $token)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid cron token.',
            ], 403);
        }

        $lock = Cache::lock('blog-news-publication-cron', 300);

        if (! $lock->get()) {
            return response()->json([
                'success' => false,
                'message' => 'Blog & News publication cron is already running.',
            ], 429);
        }

        try {
            $summary = $service->sync();

            return response()->json([
                'success' => true,
                'message' => 'Blog & News publication cron completed.',
                ...$summary,
            ]);
        } finally {
            optional($lock)->release();
        }
    }
}
