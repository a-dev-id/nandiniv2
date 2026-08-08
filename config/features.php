<?php

return [
    'disable_membership_feature' => (bool) env('DISABLE_MEMBERSHIP_FEATURE', false),
    'disable_affiliate_feature' => (bool) env('DISABLE_AFFILIATE_FEATURE', false),
    'affiliate_registration_enabled' => (bool) env(
        'AFFILIATE_REGISTRATION_ENABLED',
        env('APP_ENV', 'production') !== 'production',
    ),
    'disable_voucher_feature' => (bool) env('DISABLE_VOUCHER_FEATURE', false),
];
