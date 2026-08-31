<?php

return [
    'disable_membership_feature' => ! (bool) env('MEMBERSHIP_ENABLED', true),
    'disable_affiliate_feature' => ! (bool) env('AFFILIATE_ENABLED', true),
    'affiliate_registration_enabled' => (bool) env(
        'AFFILIATE_REGISTRATION_ENABLED',
        env('APP_ENV', 'production') !== 'production',
    ),
    'disable_voucher_feature' => ! (bool) env('VOUCHER_ENABLED', true),
];
