<?php

namespace App\Services\Affiliate;

use App\Models\AffiliateProgramSetting;
use RuntimeException;

class AffiliateBookingUrlBuilder
{
    public function build(string $affiliateCode, array $parameters = []): string
    {
        $baseUrl = AffiliateProgramSetting::current()->booking_engine_base_url;
        $parts = parse_url($baseUrl);

        if (! is_array($parts) || empty($parts['scheme']) || empty($parts['host'])) {
            throw new RuntimeException('The booking-engine base URL is not configured correctly.');
        }

        parse_str($parts['query'] ?? '', $baseQuery);
        $query = [
            ...$baseQuery,
            ...array_filter($parameters, fn (mixed $value): bool => $value !== null && $value !== ''),
            'voucher' => $affiliateCode,
            'checkin' => 'today',
        ];
        $path = $parts['path'] ?? '/';
        $port = isset($parts['port']) ? ':'.$parts['port'] : '';

        return $parts['scheme'].'://'.$parts['host'].$port.$path.'?'.http_build_query($query, '', '&', PHP_QUERY_RFC3986);
    }
}
