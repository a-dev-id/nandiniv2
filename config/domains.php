<?php

return [
    'main' => env('MAIN_DOMAIN', 'nandinibali.test'),
    'membership' => env('MEMBERSHIP_DOMAIN', env('MAIN_DOMAIN', 'nandinibali.test')),
    'voucher' => env('VOUCHER_DOMAIN', 'voucher.nandinibali.test'),
    'voucher_landing_page_id' => (int) env('VOUCHER_LANDING_PAGE_ID', 42),
];
