<?php

return [
    'main' => env('MAIN_DOMAIN', 'nandinibali.com'),
    'membership' => env('MEMBERSHIP_DOMAIN', env('MAIN_DOMAIN', 'nandinibali.com')),
    'voucher' => env('VOUCHER_DOMAIN', 'voucher.nandinibali.com'),
    'voucher_landing_page_id' => (int) env('VOUCHER_LANDING_PAGE_ID', 42),
];
