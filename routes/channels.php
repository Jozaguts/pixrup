<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('private-glowup.jobs.{propertyId}', function ($user, int $propertyId): bool {
    return $user !== null;
});
