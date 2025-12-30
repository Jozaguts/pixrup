<?php

namespace App\Infrastructure\Property\Overview\Persistence;

use App\Application\Properties\Overview\Contracts\OverviewRepository;
use App\Application\Properties\Overview\DTOs\PropertyOverviewSnapshotDTO;

final readonly class CompositeOverviewRepository implements OverviewRepository
{
    public function __construct(
        private OverviewRepository $redis,
        private OverviewRepository $eloquent,
    ) {}

    public function get(int $propertyId): ?PropertyOverviewSnapshotDTO
    {
        $data = $this->redis->get($propertyId);
        if ($data) {
            return $data;
        }

        $data = $this->eloquent->get($propertyId);
        if ($data) {
            $this->redis->save($data);
        }

        return $data;
    }

    public function save(PropertyOverviewSnapshotDTO $dto): void
    {
        $this->redis->save($dto);
        $this->eloquent->save($dto);
    }

    public function forget(int $propertyId): void
    {
        $this->redis->forget($propertyId);
        $this->eloquent->forget($propertyId);
    }
}
