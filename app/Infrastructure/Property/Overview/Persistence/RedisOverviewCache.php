<?php

namespace App\Infrastructure\Property\Overview\Persistence;

use App\Application\Properties\Overview\Contracts\OverviewRepository;
use App\Application\Properties\Overview\DTOs\PropertyOverviewSnapshotDTO;
use App\Application\Shared\Contracts\Cache\KeyValueStore;
use Carbon\CarbonImmutable;
use JsonException;

final readonly class RedisOverviewCache implements OverviewRepository
{
    public function __construct(private KeyValueStore $store) {}

    private function key(int $propertyId): string
    {
        return "overview:property:{$propertyId}";
    }

    /**
     * @throws JsonException
     */
    public function get(int $propertyId): ?PropertyOverviewSnapshotDTO
    {
        $raw = $this->store->get($this->key($propertyId));
        if (!$raw) {
            return null;
        }

        $decoded = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
        return PropertyOverviewSnapshotDTO::fromArray($decoded);
    }

    /**
     * @throws JsonException
     */
    public function save(PropertyOverviewSnapshotDTO $data): void
    {
        $this->store->put(
            $this->key($data->propertyId),
            json_encode($data->toArray(), JSON_THROW_ON_ERROR),
            $this->ttl($data)
        );
    }

    public function forget(int $propertyId): void
    {
        $this->store->forget($this->key($propertyId));
    }

    private function ttl(PropertyOverviewSnapshotDTO $data): int
    {
        $ttl = CarbonImmutable::now('UTC')->diffInSeconds($data->expiresAt, false);
        return $ttl > 0 ? $ttl : 60;
    }
}
