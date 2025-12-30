<?php

namespace App\Infrastructure\Property\Overview\Persistence;

use App\Application\Properties\Overview\Contracts\OverviewRepository;
use App\Application\Properties\Overview\DTOs\OverviewDTO;

final readonly class CompositeOverviewRepository implements OverviewRepository
{
    public function __construct(
        private OverviewRepository $redis,
        private OverviewRepository $eloquent,
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
