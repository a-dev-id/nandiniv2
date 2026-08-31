<?php

return [
    'main' => env('MAIN_DOMAIN', 'nandinibali.test'),
    'spa' => env('SPA_DOMAIN', 'spa.nandinibali.test'),
    'spa_enabled' => (bool) env('SPA_ENABLED', false),
    'membership' => env('MEMBERSHIP_DOMAIN', env('MAIN_DOMAIN', 'nandinibali.test')),
    'affiliate' => env('AFFILIATE_DOMAIN', 'affiliate.nandinibali.test'),
    'short_link' => env('SHORT_LINK_DOMAIN', 'go.nandinibali.test'),
    'short_link_scheme' => env('SHORT_LINK_SCHEME', env('APP_ENV', 'production') === 'production' ? 'https' : 'http'),
    'voucher' => env('VOUCHER_DOMAIN', 'voucher.nandinibali.test'),
    'voucher_landing_page_id' => (int) env('VOUCHER_LANDING_PAGE_ID', 42),
];
