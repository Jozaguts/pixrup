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
            "You are an expert architectural photo editor AI.\n\n"
            . "BASE RULES (always obey):\n"
            . "- Preserve original camera viewpoint, perspective, vanishing point, depth, and geometry.\n"
            . "- Maintain photorealism: natural lighting, realistic shadows, real materials.\n"
            . "- No CGI, no stylized or illustrative rendering.\n"
            . "- Output must look like a real photograph.\n\n"
            . "SCENE CONTEXT:\n"
            . "- Room type: {room}\n"
            . "- Desired style: {style}\n\n"
            . "STRUCTURAL RULES:\n"
            . "- Walls, floors, ceilings, doors, windows, and room proportions must remain accurate unless explicitly allowed.\n"
            . "- Perspective and spatial depth must never change.\n\n"
            . "USER EDIT ZONE (high priority):\n"
            . "Apply the following user-requested modifications carefully, as long as they do not break the base rules:\n"
            . "{USER_INSTRUCTIONS}\n\n"
            . "EDITING GUIDELINES:\n"
            . "- The user may request:\n"
            . "  - Removing existing objects\n"
            . "  - Adding new furniture or decor\n"
            . "  - Changing wall colors or materials\n"
            . "  - Updating finishes or lighting\n"
            . "- When removing objects, fill the space naturally and realistically.\n"
            . "- When adding objects, match scale, lighting, and style to the scene.\n"
            . "- All changes must remain architecturally plausible.\n\n"
            . "FINAL QUALITY CHECK:\n"
            . "- High detail, realistic textures\n"
            . "- Correct light direction and shadow behavior\n"
            . "- No artificial or rendered look"
        ),
        'prompt_template_sdxl' => env(
            'REPLICATE_PROMPT_TEMPLATE_SDXL',
            "You are an expert architectural photo editor AI.\n\n"
            . "BASE RULES (always obey):\n"
            . "- Preserve original camera viewpoint, perspective, vanishing point, depth, and geometry.\n"
            . "- Maintain photorealism: natural lighting, realistic shadows, real materials.\n"
            . "- No CGI, no stylized or illustrative rendering.\n"
            . "- Output must look like a real photograph.\n\n"
            . "SCENE CONTEXT:\n"
            . "- Room type: {room}\n"
            . "- Desired style: {style}\n\n"
            . "STRUCTURAL RULES:\n"
            . "- Walls, floors, ceilings, doors, windows, and room proportions must remain accurate unless explicitly allowed.\n"
            . "- Perspective and spatial depth must never change.\n\n"
            . "USER EDIT ZONE (high priority):\n"
            . "Apply the following user-requested modifications carefully, as long as they do not break the base rules:\n"
            . "{USER_INSTRUCTIONS}\n\n"
            . "EDITING GUIDELINES:\n"
            . "- The user may request:\n"
            . "  - Removing existing objects\n"
            . "  - Adding new furniture or decor\n"
            . "  - Changing wall colors or materials\n"
            . "  - Updating finishes or lighting\n"
            . "- When removing objects, fill the space naturally and realistically.\n"
            . "- When adding objects, match scale, lighting, and style to the scene.\n"
            . "- All changes must remain architecturally plausible.\n\n"
            . "FINAL QUALITY CHECK:\n"
            . "- High detail, realistic textures\n"
            . "- Correct light direction and shadow behavior\n"
            . "- No artificial or rendered look"
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
