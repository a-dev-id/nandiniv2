<?php

namespace App\Http\Controllers;

use App\Models\Affiliate;
use App\Services\Affiliate\AffiliateBookingUrlBuilder;
use App\Services\Affiliate\Click\RecordAffiliateClickService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class AffiliateShortLinkController extends Controller
{
    public function __invoke(
        string $affiliate_code,
        Request $request,
        AffiliateBookingUrlBuilder $urls,
        RecordAffiliateClickService $clicks,
    ): RedirectResponse|Response {
        $affiliate = Affiliate::query()->where('affiliate_code', $affiliate_code)->first();

        if (! $affiliate?->isApproved() || ! $affiliate->short_link_activated_at || $affiliate->short_link_slug !== $affiliate_code) {
            return response()->view('pages.affiliate.short-link-unavailable', status: 404);
        }

        $destination = $urls->build($affiliate->affiliate_code);

        try {
            $clicks->record($affiliate, $request);
        } catch (Throwable $exception) {
            Log::warning('Affiliate click analytics recording failed.', [
                'affiliate_id' => $affiliate->getKey(),
                'exception' => $exception::class,
            ]);
        }

        return redirect()->away($destination);
    }
}
