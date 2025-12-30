<?php

namespace App\Infrastructure\Property\Overview\Persistence;

use App\Application\Properties\Overview\Contracts\OverviewRepository;
use App\Application\Properties\Overview\DTOs\OverviewDTO;
use App\Models\Property;

final class EloquentOverviewRepository implements OverviewRepository
{
    public function get(int $propertyId, array $filters = []): ?OverviewDTO
    {
       $row = Property::query()
           ->where('id', $propertyId)
           ->first();
    }

    public function put(int $propertyId, OverviewDTO $data, int $ttlSeconds): void
    {
        // TODO: Implement put() method.
    }

    public function forget(int $propertyId): void
    {
        // TODO: Implement forget() method.
    }
}
