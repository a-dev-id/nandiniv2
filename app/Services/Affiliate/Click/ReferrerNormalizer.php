<?php

namespace App\Services\Affiliate\Click;

use Illuminate\Http\Request;

class ReferrerNormalizer
{
    public function normalize(Request $request): ?string
    {
        $host = parse_url((string) $request->header('Referer'), PHP_URL_HOST);

        if (! is_string($host) || $host === '') {
            return null;
        }

        $host = mb_strtolower(rtrim($host, '.'));
        $host = preg_replace('/^www\./', '', $host) ?: $host;
        $shortLinkHost = mb_strtolower((string) config('domains.short_link'));

        if ($host === $shortLinkHost || $host === 'localhost' || $host === '127.0.0.1' || $host === '::1' || str_ends_with($host, '.test')) {
            return null;
        }

        return filter_var($host, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME) ? $host : null;
    }
}
