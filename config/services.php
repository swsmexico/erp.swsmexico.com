<?php

return [

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key'    => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel'              => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    // ── Anthropic ─────────────────────────────────────────────────────────────
    'anthropic' => [
        'key' => env('ANTHROPIC_API_KEY', ''),
    ],

    // ── WhatsApp microservicio ─────────────────────────────────────────────────
    'whatsapp' => [
        'url' => env('WHATSAPP_SERVICE_URL', 'http://localhost:3001'),
    ],

    // ── Linear ────────────────────────────────────────────────────────────────
    'linear' => [
        'token' => env('LINEAR_API_TOKEN', ''),
    ],

];
