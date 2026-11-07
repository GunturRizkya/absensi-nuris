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

    /*
    |--------------------------------------------------------------------------
    | WhatsApp Gateway (notifikasi kehadiran siswa ke orang tua)
    |--------------------------------------------------------------------------
    | Driver "fonnte" mengirim lewat api.fonnte.com (token dari dashboard).
    | Driver "log" tidak mengirim apa pun, hanya menulis ke laravel.log.
    */

    'whatsapp' => [
        'driver' => env('WA_DRIVER', 'log'),
        'token' => env('WA_TOKEN'),
        'url' => env('WA_API_URL', 'https://api.fonnte.com/send'),
        'timeout' => (int) env('WA_TIMEOUT', 8),
    ],

];
