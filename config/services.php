<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI'),
    ],

    'webhotelier' => [
        // PUSH endpoint secret
        'webhook_secret' => env('WEBHOTELIER_WEBHOOK_SECRET'),

        // PULL backup credentials - fill after WebHotelier gives API access
        'api_base_url' => env('WEBHOTELIER_API_BASE_URL', 'https://rest.reserve-online.net'),
        'api_username' => env('WEBHOTELIER_API_USERNAME'),
        'api_password' => env('WEBHOTELIER_API_PASSWORD'),
        'property_code' => env('WEBHOTELIER_PROPERTY_CODE'),

        // Cron sync URL token
        'sync_token' => env('WEBHOTELIER_SYNC_TOKEN'),
    ],

    'membership_api' => [
        'url' => env('MEMBERSHIP_API_URL', 'https://membership.nandiniapps.cloud'),
        'token' => env('MEMBERSHIP_API_TOKEN'),
        'booking_sync_cron_token' => env('BOOKING_SYNC_CRON_TOKEN'),
        'timeout' => env('MEMBERSHIP_API_TIMEOUT', 20),
    ],

    'membership' => [
        'lifecycle_cron_token' => env('MEMBERSHIP_LIFECYCLE_CRON_TOKEN'),
    ],

    'welcome_email' => [
        'test_mode' => env('WELCOME_EMAIL_TEST_MODE', true),
        'test_recipient' => env('WELCOME_EMAIL_TEST_RECIPIENT'),
        'test_token' => env('WELCOME_EMAIL_TEST_TOKEN'),
    ],

    'email_relay' => [
        'url' => env('MEMBERSHIP_EMAIL_RELAY_URL'),
        'token' => env('MEMBERSHIP_EMAIL_RELAY_TOKEN'),
    ],

    'mail_test_token' => env('MAIL_TEST_TOKEN'),

];
