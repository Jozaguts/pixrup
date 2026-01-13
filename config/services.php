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
    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI'),
    ],

    'appraisal' => [
        'provider' => env('APPRAISAL_PROVIDER', 'mock'),
    ],

    'glowup' => [
        'provider' => env('GLOWUP_PROVIDER', 'fake'),
    ],

    'replicate' => [
        'token' => env('REPLICATE_API_TOKEN'),
        'base_url' => env('REPLICATE_API_BASE_URL', 'https://api.replicate.com/v1/'),
        'model_owner' => env('REPLICATE_MODEL_OWNER', 'bytedance'),
        'model' => env('REPLICATE_MODEL', 'seedream-4'),
        'prompt_template' => env(
            'REPLICATE_PROMPT_TEMPLATE',
            "Photorealistic renovation of the provided reference photo ({room}) in {style} style. " .
            "CRITICAL: preserve the exact camera viewpoint, lens perspective, vanishing point, depth, and room proportions. " .
            "Do not change layout or geometry: keep walls, floor/ceiling boundaries, doors, windows, hallway length and angles identical. " .
            "Only upgrade materials, finishes, lighting, and decor. Natural lighting, realistic shadows, high detail."
        ),
        'negative_prompt_template' => env(
            'REPLICATE_NEGATIVE_PROMPT_TEMPLATE',
            "warped geometry, incorrect perspective, fisheye, bent lines, crooked walls, stretched hallway, " .
            "extra doors, extra windows, duplicated objects, floating furniture, unrealistic scale, text, watermark, logo, blurry, low-res"
        ),
        'size' => env('REPLICATE_IMAGE_SIZE', '2K'),
        'aspect_ratio' => env('REPLICATE_ASPECT_RATIO', '4:3'),
        'max_images' => env('REPLICATE_MAX_IMAGES', 1),
        'wait_preference' => env('REPLICATE_WAIT_PREFERENCE', 'wait=60'),
        'timeout' => env('REPLICATE_TIMEOUT', 120),
        'retries' => env('REPLICATE_RETRIES', 2),
    ],

    'rentcast' => [
        'api_key' => env('RENT_CAST_API_KEY'),
        'base_url' => env('RENT_CAST_API_BASE_URL', 'https://api.rentcast.io/v1/'),
    ],
    'house_canary' => [
        'api_key' => env('HC_API_KEY'),
        'secret' => env('HC_API_SECRET'),
        'base_url' => env('HC_API_BASE_URL', 'https://api.housecanary.com/'),
    ]

];
