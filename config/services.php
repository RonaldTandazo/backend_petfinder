<?php

$isProduction = env('APP_ENV') === 'production';

$picturesHost = env('AWS_URL_LOCAL', 'http://localhost:9000')
    . '/' . env('AWS_BUCKET_LOCAL', 'petfinder-pictures')
    . '/';

if ($isProduction) {
    $picturesHost = env('AWS_URL')
        . '/' . env('AWS_BUCKET', 'petfinder-pictures')
        . '/';
}

return [

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

    'minio' => [
        'webhook_token' => env('MINIO_WEBHOOK_TOKEN'),
    ],

    'pictures' => [
        'host' => $picturesHost,
    ],
];