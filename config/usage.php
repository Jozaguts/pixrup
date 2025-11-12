<?php

return [
    'timezone' => env('USAGE_TIMEZONE', 'UTC'),
    'reset_cron_time' => env('USAGE_RESET_TIME', '00:10'),
    'retention_months' => (int) env('USAGE_RETENTION_MONTHS', 12),
];
