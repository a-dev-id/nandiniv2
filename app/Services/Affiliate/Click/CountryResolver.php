<?php

namespace App\Services\Affiliate\Click;

use App\Support\InquiryOptions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use MaxMind\Db\Reader;
use Symfony\Component\HttpFoundation\IpUtils;
use Throwable;

class CountryResolver
{
    /** @return array{code: ?string, name: ?string} */
    public function resolve(Request $request): array
    {
        $header = trim((string) config('affiliate-clicks.country_header'));
        $peer = (string) $request->server('REMOTE_ADDR');
        $trustedProxies = config('affiliate-clicks.trusted_proxies', []);

        if ($header !== '' && $peer !== '' && $trustedProxies !== [] && IpUtils::checkIp($peer, $trustedProxies)) {
            $country = $this->normalize((string) $request->header($header));

            if ($country['code'] !== null) {
                return $country;
            }
        }

        return $this->resolveFromLocalDatabase((string) $request->ip());
    }

    /** @return array{code: ?string, name: ?string} */
    private function resolveFromLocalDatabase(string $ip): array
    {
        $path = trim((string) config('affiliate-clicks.geoip_database'));

        if ($path === '') {
            return ['code' => null, 'name' => null];
        }

        if (! is_readable($path) || ! class_exists(Reader::class)) {
            Log::warning('Affiliate country resolver unavailable.', ['source' => 'local_database']);

            return ['code' => null, 'name' => null];
        }

        try {
            $reader = new Reader($path);
            $record = $reader->get($ip);
            $reader->close();

            return $this->normalize((string) data_get($record, 'country.iso_code'));
        } catch (Throwable) {
            Log::warning('Affiliate country resolver unavailable.', ['source' => 'local_database']);

            return ['code' => null, 'name' => null];
        }
    }

    /** @return array{code: ?string, name: ?string} */
    private function normalize(string $value): array
    {
        $code = mb_strtoupper(trim($value));

        if (! preg_match('/^[A-Z]{2}$/', $code) || in_array($code, ['XX', 'T1'], true)) {
            return ['code' => null, 'name' => null];
        }

        $countries = InquiryOptions::countryCodes();

        return isset($countries[$code])
            ? ['code' => $code, 'name' => $countries[$code]]
            : ['code' => null, 'name' => null];
    }
}
