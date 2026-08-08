<?php

return [
    'visitor_hash_key' => env('AFFILIATE_CLICK_HASH_KEY') ?: env('APP_KEY'),
    'country_header' => env('AFFILIATE_COUNTRY_HEADER', 'CF-IPCountry'),
    'geoip_database' => env('AFFILIATE_GEOIP_DATABASE'),
    'retention_days' => (int) env('AFFILIATE_CLICK_RETENTION_DAYS', 395),
    'trusted_proxies' => array_values(array_filter(array_map(
        'trim',
        explode(',', (string) env('TRUSTED_PROXIES', '')),
    ))),
];
