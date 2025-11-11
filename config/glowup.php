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
        ['value' => 'living_room', 'label' => 'Sala / Living'],
        ['value' => 'kitchen', 'label' => 'Cocina'],
        ['value' => 'bathroom', 'label' => 'Baño'],
        ['value' => 'bedroom', 'label' => 'Dormitorio'],
        ['value' => 'dining_room', 'label' => 'Comedor'],
        ['value' => 'facade', 'label' => 'Fachada'],
        ['value' => 'outdoor', 'label' => 'Exterior / Patio'],
        ['value' => 'office', 'label' => 'Oficina / Estudio'],
    ],
    'styles' => [
        ['value' => 'modern', 'label' => 'Moderno'],
        ['value' => 'minimalist', 'label' => 'Minimalista'],
        ['value' => 'luxury', 'label' => 'Lujo'],
        ['value' => 'rustic', 'label' => 'Rústico'],
        ['value' => 'industrial', 'label' => 'Industrial'],
        ['value' => 'scandinavian', 'label' => 'Escandinavo'],
        ['value' => 'mediterranean', 'label' => 'Mediterráneo'],
        ['value' => 'outdoor_resort', 'label' => 'Resort Exterior'],
    ],
    'default_poll_interval_ms' => 2800,
    'broadcast_channel_prefix' => 'private-glowup.jobs.',
];
