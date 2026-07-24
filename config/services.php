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

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
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
   'google_ai' => [
        'api_key' => env('GOOGLE_AI_API_KEY'),

    ],

    'turnstile' => [
        'site_key' => env('TURNSTILE_SITE_KEY'),
        'secret_key' => env('TURNSTILE_SECRET_KEY'),
    ],

    
    'ai_provider' => [
        'key'   => env('AI_PROVIDER_KEY', 'sk_2Yf5QKon74UpiClRTYZtWGdyb3FYkjney9Fel9NmIe1exZeNvjsb'),
        'url'   => env('AI_PROVIDER_URL', 'https://api.groq.com/openai/v1/chat/completions'),
        'model' => env('AI_MODEL', 'llama-3.1-8b-instant'),
    ],
];
