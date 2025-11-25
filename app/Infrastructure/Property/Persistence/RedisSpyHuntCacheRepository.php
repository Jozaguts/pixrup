<?php

namespace App\Infrastructure\Property\Persistence;

use App\Application\Properties\DTOs\SpyHuntDataDTO;
use App\Domain\Properties\Repositories\SpyHuntCacheRepositoryInterface;
use Illuminate\Support\Facades\Redis;

class RedisSpyHuntCacheRepository implements SpyHuntCacheRepositoryInterface
{
    private function key(int $propertyId): string
    {
        return "spyhunt:property:{$propertyId}";
    }

    public function get(int $propertyId): ?SpyHuntDataDTO
    {
        $raw = Redis::get($this->key($propertyId));

        if (!$raw) {
            return null;
        }
        $decoded = json_decode($raw, true);

        return new SpyHuntDataDTO(
            property: $decoded['property'],
            filters: $decoded['filters'],
            marketSnapshot: $decoded['market_snapshot'],
            valueEstimate: $decoded['value_estimate'],
            comparables: $decoded['comparables'],
            stats: $decoded['stats'],
            source: $decoded['source']
        );

    }

    public function put(int $propertyId, SpyHuntDataDTO $data, int $ttlInSeconds): void
    {
        Redis::setex(
            $this->key($propertyId), $ttlInSeconds,json_encode($data->toArray())
        );
    }

    public function forget(int $propertyId): void
    {
        Redis::del($this->key($propertyId));
    }
}