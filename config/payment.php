<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Default Payment Gateway Driver
    |--------------------------------------------------------------------------
    |
    | Menentukan driver Payment Gateway Aggregator default yang aktif.
    | Opsi yang didukung: 'midtrans', 'xendit', 'tripay', 'simulated'
    |
    */
    'default' => env('PAYMENT_GATEWAY_DRIVER', 'midtrans'),

    /*
    |--------------------------------------------------------------------------
    | Payment Gateway Providers
    |--------------------------------------------------------------------------
    */
    'providers' => [
        'midtrans' => [
            'merchant_id' => env('MIDTRANS_MERCHANT_ID', ''),
            'server_key' => env('MIDTRANS_SERVER_KEY', 'SB-Mid-server-demo-key'),
            'client_key' => env('MIDTRANS_CLIENT_KEY', 'SB-Mid-client-demo-key'),
            'is_production' => env('MIDTRANS_IS_PRODUCTION', false),
            'is_sanitized' => env('MIDTRANS_IS_SANITIZED', true),
            'is_3ds' => env('MIDTRANS_IS_3DS', true),
            'snap_url' => env('MIDTRANS_IS_PRODUCTION', false)
                ? 'https://app.midtrans.com/snap/snap.js'
                : 'https://app.sandbox.midtrans.com/snap/snap.js',
            'api_url' => env('MIDTRANS_IS_PRODUCTION', false)
                ? 'https://api.midtrans.com/v2'
                : 'https://api.sandbox.midtrans.com/v2',
        ],

        'xendit' => [
            'secret_key' => env('XENDIT_SECRET_KEY', 'xnd_development_demo_key'),
            'public_key' => env('XENDIT_PUBLIC_KEY', 'xnd_public_demo_key'),
            'webhook_token' => env('XENDIT_WEBHOOK_TOKEN', 'xendit_webhook_secret'),
            'api_url' => 'https://api.xendit.co',
        ],

        'tripay' => [
            'api_key' => env('TRIPAY_API_KEY', ''),
            'private_key' => env('TRIPAY_PRIVATE_KEY', ''),
            'merchant_code' => env('TRIPAY_MERCHANT_CODE', ''),
            'is_production' => env('TRIPAY_IS_PRODUCTION', false),
            'api_url' => env('TRIPAY_IS_PRODUCTION', false)
                ? 'https://tripay.co.id/api'
                : 'https://tripay.co.id/api-sandbox',
        ],

        'simulated' => [
            'name' => 'PUTRIJALVA Local PGA Simulator',
            'auto_settle' => env('SIMULATED_PAYMENT_AUTO_SETTLE', false),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Webhook & Notification Security
    |--------------------------------------------------------------------------
    */
    'webhook' => [
        'tolerance_seconds' => 300,
        'log_payload' => env('PAYMENT_LOG_PAYLOAD', true),
    ],
];
