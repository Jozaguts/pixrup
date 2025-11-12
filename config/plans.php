<?php

return [
    'default' => 'professional',
    'tiers' => [
        'professional' => [
            'label' => 'Professional',
            'limit' => 20,
            'features' => [
                'appraisal',
                'glowup',
                'spyhunt',
                'report',
            ],
            'price' => 99,
        ],
        'business' => [
            'label' => 'Business',
            'limit' => 50,
            'features' => [
                'appraisal',
                'glowup',
                'spyhunt',
                'report',
                'priority_support',
            ],
            'price' => 199,
        ],
        'enterprise' => [
            'label' => 'Enterprise',
            'limit' => 200,
            'features' => [
                'appraisal',
                'glowup',
                'spyhunt',
                'report',
                'priority_support',
                'white_label',
                'api_access',
                'teams',
            ],
            'price' => 499,
        ],
    ],
    'aliases' => [
        'pro' => 'professional',
        'professional' => 'professional',
        'business' => 'business',
        'enterprise' => 'enterprise',
    ],
];
