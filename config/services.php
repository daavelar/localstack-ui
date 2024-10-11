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
        'token' => env('POSTMARK_TOKEN'),
    ],

    'aws' => [
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
        'id' => env('AWS_ACCESS_KEY_ID', 'localstack'),
        'secret' => env('AWS_SECRET_ACCESS_KEY', 'localstack'),
        'endpoint' => env('AWS_ENDPOINT', 'http://localstack:4566'),
        'token' => env('AWS_SESSION_TOKEN', '123'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

];
