<?php

namespace App\Domain\Properties\Repositories;

use App\Application\Properties\DTOs\SpyHuntDataDTO;

interface SpyHuntCacheRepositoryInterface
{
    public function get(int $propertyId, array $filters): ?SpyHuntDataDTO;

    public function put(int $propertyId, SpyHuntDataDTO $data,  int $ttlInSeconds): void;

    public function forget(int $propertyId): void;
}