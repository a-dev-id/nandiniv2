<?php

namespace App\Support;

use App\Models\Member;
use Illuminate\Support\Facades\Auth;

class MemberBookingVoucher
{
    private const BOOKING_HOST = 'nandinijunglebyhanginggardens.reserve-online.net';

    public static function appendToUrl(?string $url): ?string
    {
        if (! filled($url)) {
            return $url;
        }

        $url = html_entity_decode((string) $url, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        if (! self::isBookingUrl($url)) {
            return $url;
        }

        $url = self::normalizeBookingHost($url);

        if (config('features.disable_membership_feature')) {
            return $url;
        }

        $voucher = self::currentVoucherCode();

        if (! $voucher) {
            return $url;
        }

        $parts = parse_url($url);

        if ($parts === false) {
            return $url;
        }

        parse_str($parts['query'] ?? '', $query);

        $query['voucher'] = $voucher;

        $scheme = isset($parts['scheme']) ? $parts['scheme'] . '://' : '';
        $host = $parts['host'] ?? '';
        $port = isset($parts['port']) ? ':' . $parts['port'] : '';
        $path = $parts['path'] ?? '/';
        $fragment = isset($parts['fragment']) ? '#' . $parts['fragment'] : '';
        $queryString = http_build_query($query);

        return $scheme . $host . $port . $path . ($queryString ? '?' . $queryString : '') . $fragment;
    }

    public static function currentVoucherCode(): ?string
    {
        $member = Auth::guard('member')->user();

        if (! $member instanceof Member) {
            return null;
        }

        $tier = strtolower((string) ($member->tier ?: Member::getTierByPoints((int) $member->points)));

        return match ($tier) {
            Member::TIER_SILVER => 'UPAYA',
            Member::TIER_GOLD => 'DHYANA',
            Member::TIER_PLATINUM => 'JNANA',
            default => 'DANA',
        };
    }

    private static function isBookingUrl(string $url): bool
    {
        $host = strtolower((string) parse_url($url, PHP_URL_HOST));

        return str_ends_with($host, '.reserve-online.net')
            || $host === 'reserve-online.net';
    }

    private static function normalizeBookingHost(string $url): string
    {
        $parts = parse_url($url);

        if ($parts === false) {
            return $url;
        }

        $path = $parts['path'] ?? '/';
        $query = isset($parts['query']) ? '?' . $parts['query'] : '';
        $fragment = isset($parts['fragment']) ? '#' . $parts['fragment'] : '';

        return 'https://' . self::BOOKING_HOST . $path . $query . $fragment;
    }
}
