<?php

namespace App\Application\Properties\SpyHunt\Contracts;

use App\Application\Properties\DTOs\SpyHuntDataDTO;

interface SpyHuntCacheRepository
{
    public function get(int $propertyId): ?SpyHuntDataDTO;
    public function put(int $propertyId, SpyHuntDataDTO $data, int $ttlSeconds): void;
    public function forget(int $propertyId): void;
}
