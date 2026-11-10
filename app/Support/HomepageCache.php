<?php

namespace App\Support;

use Closure;
use Illuminate\Support\Facades\Cache;

class HomepageCache
{
    public const KEY = 'homepage:public-content:v2';

    public const TTL_SECONDS = 300;

    public static function remember(Closure $callback): array
    {
        return Cache::remember(self::KEY, self::TTL_SECONDS, $callback);
    }

    public static function forget(): void
    {
        Cache::forget(self::KEY);
    }
}
