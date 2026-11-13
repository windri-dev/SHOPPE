<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Resend, Postmark, AWS, and more. This file provides the de facto
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

    'ai_concierge' => [
        'provider' => env('AI_CONCIERGE_PROVIDER', 'gemini'),
        'api_key' => env('AI_CONCIERGE_API_KEY'),
        'model' => env('AI_CONCIERGE_MODEL', 'ag/gemini-3.8-flash-low'),
        'endpoint' => env('AI_CONCIERGE_ENDPOINT'),
    ],

    'currency' => [
        'api_url' => env('CURRENCY_API_URL', 'https://open.er-api.com/v6/latest/USD'),
        'default_usd_to_idr' => (float) env('CURRENCY_DEFAULT_USD_TO_IDR', 16000.0),
        'cache_ttl' => (int) env('CURRENCY_CACHE_TTL', 86400),
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI'),
    ],

    'whatsapp' => [
        'driver' => env('WHATSAPP_DRIVER', 'log'),
        'api_token' => env('WHATSAPP_API_TOKEN'),
        'api_url' => env('WHATSAPP_API_URL', 'https://api.fonnte.com/send'),
        'concierge_phone' => env('WHATSAPP_CONCIERGE_PHONE', '6281234567890'),
    ],

    'shipping' => [
        'free_shipping_threshold_idr' => (float) env('SHIPPING_FREE_THRESHOLD_IDR', 500000.0),
        'rajaongkir_api_key' => env('RAJAONGKIR_API_KEY'),
        'origin_city_id' => env('SHIPPING_ORIGIN_CITY_ID', '152'),
    ],
];
