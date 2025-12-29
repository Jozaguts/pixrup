<?php

namespace App\Infrastructure\Property\Persistence;

use App\Application\Properties\Overview\Contracts\OverviewCache;
use App\Application\Properties\Overview\DTOs\OverviewDTO;

final readonly class CompositeOverviewCache implements OverviewCache
{
    public function __construct(
        private OverviewCache $redis,
        private OverviewCache $eloquent,
        private int $ttlSeconds = 86400,
    ) {}
    public function get(int $propertyId, array $filters = []): ?OverviewDTO
    {
        $data = $this->redis->get($propertyId, $filters);
        if ($data) {
            return $data;
        }

        $data = $this->eloquent->get($propertyId, $filters);
        if ($data) {
            $this->redis->put($propertyId, $data, $this->ttlSeconds);
            return $data;
        }

        return null;
    }

    public function put(int $propertyId, OverviewDTO $data, int $ttlSeconds): void
    {
        $this->redis->put($propertyId, $data, $ttlSeconds);
        $this->eloquent->put($propertyId, $data, $ttlSeconds);
    }

    public function forget(int $propertyId): void
    {
        $this->redis->forget($propertyId);
        $this->eloquent->forget($propertyId);
    }
}