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

    'whatsapp' => [
        'number' => env('WHATSAPP_NUMBER', '2348103996947'),
    ],

    'store' => [
        'phone'    => env('STORE_PHONE', '0810 399 6947'),
        'email'    => env('STORE_EMAIL', 'atlascollection6@gmail.com'),
        'location' => env('STORE_LOCATION', 'Wunti market, Bababa plaza, shop E7 Block E (Beside the New Flyover), Bauchi, Nigeria'),
    ],

    'social' => [
        'instagram' => env('SOCIAL_INSTAGRAM', 'https://instagram.com/atlasunisex'),
        'facebook'  => env('SOCIAL_FACEBOOK', 'https://facebook.com/atlasunisex'),
        'tiktok'    => env('SOCIAL_TIKTOK', 'https://tiktok.com/@atlasunisex'),
    ],

    'bulksms_nigeria' => [
        'api_token' => env('BULKSMS_NIGERIA_API_TOKEN'),
        'sender_id' => env('BULKSMS_NIGERIA_SENDER_ID', 'ATLAS'),
        'recipient' => env('STORE_PHONE', '08103996947'),
    ],

];
