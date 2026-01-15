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
        'defect_detection_url' => 'models/openai/gpt-4.1-mini/predictions',
        'model_owner' => env('REPLICATE_MODEL_OWNER', 'bytedance'),
        'model' => env('REPLICATE_MODEL', 'seedream-4.5'),
        'version' => env(
            'REPLICATE_MODEL_VERSION',
            '610dddf033f10431b1b55f24510b6009fcba23017ee551a1b9afbc4eec79e29c'
        ),
        'prompt_template_seedream' => env(
            'REPLICATE_PROMPT_TEMPLATE_SEEDREAM',
            "Edit the provided reference photo of a {room}. Apply a tasteful {style} glow-up.\n\n"
            . "Strictly preserve the original camera viewpoint, perspective/vanishing point, depth, and all geometry.\n"
            . "Do not change the layout or structure: keep walls, floor/ceiling lines, doors/windows, corridor length/angles.\n"
            . "Keep all existing objects and their positions (bed/sofa/chairs/cabinets). You may improve materials, finishes, lighting, and decor, but do not remove, add, or relocate items.\n\n"
            . "Output must look like a real photo: photorealistic, natural lighting, realistic shadows, high detail. No CGI / no stylized render."
        ),
        'prompt_template_sdxl' => env(
            'REPLICATE_PROMPT_TEMPLATE_SDXL',
            "Enhance the provided photo with subtle, realistic upgrades for a {style} {room}.\n"
            . "Preserve the original layout, perspective, depth, proportions, and camera angle.\n"
            . "Do NOT change the structure, room shape, walls, doors, windows, or furniture placement.\n"
            . "Improve materials, finishes, lighting, textures, and overall aesthetics only.\n"
            . "Keep all existing objects in their original positions.\n"
            . "Photorealistic, natural lighting, realistic shadows."
        ),
        'negative_prompt_template_sdxl' => env(
            'REPLICATE_NEGATIVE_PROMPT_TEMPLATE_SDXL',
            "new layout, new room, altered geometry, incorrect perspective, "
            . "warped walls, moved furniture, extra objects, missing objects, "
            . "fantasy, CGI look, cartoon, illustration, dramatic redesign"
        ),
        'size' => env('REPLICATE_IMAGE_SIZE', '2K'),
        'aspect_ratio' => env('REPLICATE_ASPECT_RATIO', '4:3'),
        'max_images' => env('REPLICATE_MAX_IMAGES', 1),
        'strength' => env('REPLICATE_IMAGE_STRENGTH', 0.85),
        'guidance_scale' => env('REPLICATE_GUIDANCE_SCALE', 6),
        'steps' => env('REPLICATE_STEPS', 35),
        'width' => env('REPLICATE_WIDTH', 2048),
        'height' => env('REPLICATE_HEIGHT', 2048),
        'wait_preference' => env('REPLICATE_WAIT_PREFERENCE', 'wait=60'),
        'timeout' => env('REPLICATE_TIMEOUT', 120),
        'retries' => env('REPLICATE_RETRIES', 2),
        'models' => [
            'defect_detection' => env(
                'REPLICATE_DEFECT_DETECTION_MODEL',
                'openai/gpt-4.1-mini-vision'
            ),
        ],
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
