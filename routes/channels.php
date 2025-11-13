<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('glowup.jobs.{propertyId}', function ($user, int $propertyId): bool {
    return true;
});
