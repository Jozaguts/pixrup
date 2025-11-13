<?php

return [
    'disk' => env('GLOWUP_DISK', env('FILESYSTEM_DISK', 'public')),
    'feature_identifier' => 'glowup.generate',
    'max_upload_size_mb' => (int) env('GLOWUP_MAX_UPLOAD_MB', 10),
    'allowed_mimes' => [
        'image/jpeg',
        'image/png',
        'image/webp',
        'image/jpg',
        'image/heic',
        'image/heif',
    ],
    'room_types' => [
        ['value' => 'living_room', 'label' => 'Living room'],
        ['value' => 'kitchen', 'label' => 'Kitchen'],
        ['value' => 'bathroom', 'label' => 'Bathroom'],
        ['value' => 'bedroom', 'label' => 'Bedroom'],
        ['value' => 'dining_room', 'label' => 'Dining room'],
        ['value' => 'facade', 'label' => 'Facade'],
        ['value' => 'outdoor', 'label' => 'Outdoor / Patio'],
        ['value' => 'office', 'label' => 'Office / Studio'],
    ],
    'styles' => [
        ['value' => 'modern', 'label' => 'Modern'],
        ['value' => 'minimalist', 'label' => 'Minimalist'],
        ['value' => 'luxury', 'label' => 'Luxury'],
        ['value' => 'rustic', 'label' => 'Rustic'],
        ['value' => 'industrial', 'label' => 'Industrial'],
        ['value' => 'scandinavian', 'label' => 'Scandinavian'],
        ['value' => 'mediterranean', 'label' => 'Mediterranean'],
        ['value' => 'outdoor_resort', 'label' => 'Outdoor resort'],
    ],
    'default_poll_interval_ms' => 2800,
    'broadcast_channel_prefix' => 'glowup.jobs.',
];
