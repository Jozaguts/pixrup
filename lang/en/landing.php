<?php

declare(strict_types=1);

return [
    'nav' => [
        'features' => 'Features',
        'use_cases' => 'Use cases',
        'pricing' => 'Pricing',
        'blog' => 'Blog',
        'support' => 'Support',
        'cta' => [
            'dashboard' => 'Dashboard',
            'get_started' => 'Get started',
            'sign_up' => 'Sign up',
            'log_in' => 'Log in',
        ],
        'sr' => [
            'home' => 'Home',
            'toggle_navigation' => 'Toggle navigation',
            'close_menu' => 'Close menu',
        ],
    ],
    'page' => [
        'title' => 'Welcome',
        'intro' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Deserunt, doloribus ducimus ipsum iusto maxime nisi officia pariatur quam vel voluptates? Cumque pariatur, soluta! Ab adipisci, alias asperiores aspernatur consequuntur culpa deserunt dicta, ea eaque eius facilis incidunt maiores quis quisquam repellat suscipit vitae voluptas? Animi architecto delectus deleniti distinctio eaque, eius enim error fugit, illo in ipsa itaque iure labore libero minima minus, odit placeat quae quasi qui quis ratione reprehenderit sed ullam unde vel veniam. Aliquam exercitationem id nisi perferendis voluptates. Aut delectus eius expedita iure maxime minima nihil non officiis provident quam quo sint unde, vel veniam voluptate.',
    ],
    'hero' => [
        'title' => 'Turn Any Property Into Cash.',
        'subtitle' => 'Upload. Appraise. Reimagine. Share.',
    ],
    'address_search' => [
        'placeholder' => 'Search an address...',
        'loading' => 'Loading Google Places...',
        'errors' => [
            'missing_api_key' => 'Google Maps API key is missing. Set VITE_GOOGLE_MAPS_KEY to enable address search.',
            'missing_place' => 'Unable to fetch address details from Google Places.',
            'unexpected' => 'Unexpected error loading Google Places.',
        ],
    ],
    'continue' => [
        'alerts' => [
            'select_address' => 'Select an address to continue in the app.',
            'open_on_phone' => 'Open Pixrup on your phone to continue in the app.',
        ],
        'alt' => [
            'apple' => 'Apple App Store',
            'android' => 'Google Play',
        ],
    ],
    'features' => [
        'badge' => 'Features',
        'title' => 'AI-powered creation, built for speed.',
        'description' => 'Pixrup helps you generate, transform, and deliver on-brand content in minutes—secure, collaborative, and PWA-ready for any team.',
        'note' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Adipisci, eveniet.',
        'cta' => 'Read more',
    ],
    'use_cases' => [
        'aria_label' => 'Use cases',
        'badge' => 'Use cases',
        'title' => 'Real-world scenarios, real AI results.',
        'description' => 'Explore how Pixrup is used across different workflows—each case shows real outputs, from AI transformations to market intelligence and reports, displayed through visual galleries of actual results.',
        'testimonial' => [
            'name' => 'Darrell Steward',
            'role' => 'Head of Operations, Finlytics',
            'initials' => 'DS',
        ],
        'before_alt' => 'Before image',
        'after_alt' => 'After image',
    ],
    'pricing' => [
        'badge' => 'Pricing',
        'title' => 'Select the pricing plan that best suits your needs.',
        'billing' => [
            'monthly' => 'Monthly',
            'yearly' => 'Yearly',
            'toggle_aria' => 'Toggle between monthly and yearly pricing',
            'per_month' => 'Per Month',
            'per_year' => 'Per Year',
        ],
        'cta' => 'Get started',
        'plans' => [
            'simplified' => [
                'name' => 'Simplified',
                'description' => 'For individuals and small teams with unlimited trial access.',
                'features' => [
                    'single_payment' => 'Single Payment',
                    'sell_items' => 'Selling your own items',
                    'integrations' => 'Powerful integration',
                ],
            ],
            'basic' => [
                'name' => 'Basic',
                'description' => 'For individuals and small teams with unlimited trial access.',
                'features' => [
                    'bandwidth' => 'Unlimited Bandwidth',
                    'promo_tools' => 'Promotional Tools',
                    'single_payment' => 'Single Payment',
                    'sell_items' => 'Selling your own items',
                    'integrations' => 'Powerful integration',
                ],
            ],
            'enhanced' => [
                'name' => 'Enhanced',
                'description' => 'For individuals and small teams with unlimited trial access.',
                'features' => [
                    'sell_conditions' => 'Selling on your own conditions',
                    'seamless_integrations' => 'Seamless integrations',
                    'real_time' => 'Real-time streaming',
                ],
            ],
        ],
    ],
    'support' => [
        'aria_label' => 'Contact Information and Form',
        'badge' => 'Support',
        'title' => 'Reach out to our support team.',
        'description' => 'Whether you have a question, need technical assistance, or just want some guidance, our support team is here to help. We\'re available around the clock to provide quick and friendly support.',
        'form' => [
            'name' => [
                'label' => 'Your name',
                'placeholder' => 'Enter your name',
            ],
            'phone' => [
                'label' => 'Your number',
                'placeholder' => 'Enter your number',
            ],
            'email' => [
                'label' => 'Email address',
                'placeholder' => 'Enter your email',
            ],
            'subject' => [
                'label' => 'Subject',
                'placeholder' => 'Enter your subject',
            ],
            'message' => [
                'label' => 'Write message',
                'placeholder' => 'Enter your messages',
            ],
            'terms' => [
                'text' => 'I agree with the',
                'link' => 'terms and conditions',
            ],
            'submit' => 'Submit',
        ],
    ],
    'worth_preview' => [
        'description' => 'Quick PixrWorth preview',
        'estimated_label' => 'Estimated Value',
        'estimated_fallback' => 'Coming soon',
        'estimated_note' => 'Estimated using recent sales nearby. Connect HouseCanary for live data.',
        'comparable_label' => 'Comparable Properties',
        'comparable_empty' => 'Comparable properties will appear here when available.',
        'comparable_unit_a' => 'Unit A',
        'comparable_unit_b' => 'Unit B',
        'cta' => 'Appraise Full Property',
    ],
    'footer' => [
        'rights' => 'All Rights Reserved © :year Pixrup',
        'terms' => 'Terms',
        'and' => 'and',
        'privacy' => 'Privacy Policy',
    ],
];
