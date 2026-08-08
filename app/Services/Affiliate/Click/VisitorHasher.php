<?php

namespace App\Services\Affiliate\Click;

use App\Models\Affiliate;
use Illuminate\Http\Request;
use RuntimeException;

class VisitorHasher
{
    public function hash(Affiliate $affiliate, Request $request): string
    {
        $key = (string) config('affiliate-clicks.visitor_hash_key');

        if (trim($key) === '') {
            throw new RuntimeException('Affiliate click hash key is not configured.');
        }

        $userAgent = mb_strtolower(trim((string) $request->userAgent()));
        $userAgent = preg_replace('/\d+(?:\.\d+)*/', '*', $userAgent) ?? $userAgent;
        $userAgent = preg_replace('/\s+/', ' ', $userAgent) ?? $userAgent;

        return hash_hmac('sha256', implode('|', [
            (string) $affiliate->getKey(),
            (string) $request->ip(),
            $userAgent,
        ]), $key);
    }
}
