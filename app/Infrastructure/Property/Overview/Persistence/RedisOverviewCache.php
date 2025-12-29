<?php

namespace App\Infrastructure\Property\Overview\Persistence;

use App\Application\Properties\Overview\Contracts\OverviewCache;
use App\Application\Properties\Overview\Dtos\OverviewDTO;
use App\Application\Shared\Contracts\Cache\KeyValueStore;
use JsonException;

final readonly class RedisOverviewCache implements OverviewCache
{
    public function __construct(private KeyValueStore $store) {}


    private function key(int $propertyId): string
    {
        return "overview:property:{$propertyId}";
    }

    /**
     * @throws JsonException
     */
    public function get(int $propertyId, array $filters = []): ?OverviewDTO
    {
        $raw = $this->store->get($this->key($propertyId));
        if (!$raw) {
            return null;
        }

        $decoded = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
        return OverviewDTO::fromArray($decoded);
    }

    /**
     * @throws JsonException
     */
    public function put(int $propertyId, OverviewDTO $data, int $ttlSeconds): void
    {
        $this->store->put(
            $this->key($propertyId),
            json_encode($data->toArray(), JSON_THROW_ON_ERROR),
            $ttlSeconds
        );
    }

    public function forget(int $propertyId): void
    {
        $this->store->forget($this->key($propertyId));
    }
}