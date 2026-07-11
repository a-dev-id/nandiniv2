<?php

return [
    'main' => env('MAIN_DOMAIN', 'nandinibali.test'),
    'membership' => env('MEMBERSHIP_DOMAIN', env('MAIN_DOMAIN', 'nandinibali.test')),
    'voucher' => env('VOUCHER_DOMAIN', 'voucher.nandinibali.test'),
];
