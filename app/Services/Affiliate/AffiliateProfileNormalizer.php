<?php

namespace App\Services\Affiliate;

use Illuminate\Support\Str;

class AffiliateProfileNormalizer
{
    private const SOCIAL_BASE_URLS = [
        'instagram' => 'https://www.instagram.com/',
        'facebook' => 'https://www.facebook.com/',
        'tiktok' => 'https://www.tiktok.com/@',
        'x' => 'https://x.com/',
        'threads' => 'https://www.threads.net/@',
    ];

    /** @return array<string, mixed> */
    public function normalize(array $data): array
    {
        $data['name'] = preg_replace('/\s+/u', ' ', trim((string) ($data['name'] ?? '')));
        $data['email'] = Str::lower(trim((string) ($data['email'] ?? '')));
        $data['phone_whatsapp'] = preg_replace('/\s+/u', ' ', trim((string) ($data['phone_whatsapp'] ?? '')));

        foreach (array_keys(self::SOCIAL_BASE_URLS) as $platform) {
            $data[$platform] = $this->social($platform, $data[$platform] ?? null);
        }

        return $data;
    }

    public function hasSocialProfile(array $data): bool
    {
        foreach (array_keys(self::SOCIAL_BASE_URLS) as $platform) {
            if (filled($data[$platform] ?? null)) {
                return true;
            }
        }

        return false;
    }

    private function social(string $platform, mixed $value): ?string
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        if (filter_var($value, FILTER_VALIDATE_URL)) {
            $scheme = strtolower((string) parse_url($value, PHP_URL_SCHEME));

            return $scheme === 'http' ? 'https'.substr($value, 4) : $value;
        }

        $username = ltrim($value, '@');

        if (preg_match('/^[\pL\pN._-]+$/u', $username) === 1) {
            return self::SOCIAL_BASE_URLS[$platform].rawurlencode($username);
        }

        return $value;
    }
}
