<?php

namespace App\Application\Properties\Overview\Contracts;

use App\Application\Properties\Overview\DTOs\OverviewDTO;

interface OverviewRepository
{
    public function get(int $propertyId, array $filters = []): ?OverviewDTO;
    public function put(int $propertyId, OverviewDTO $data, int $ttlSeconds): void;
    public function forget(int $propertyId): void;
}
