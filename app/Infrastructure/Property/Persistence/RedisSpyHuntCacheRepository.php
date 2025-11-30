<?php

namespace App\Infrastructure\Property\Persistence;

use App\Application\Properties\DTOs\ComparableDTO;
use App\Application\Properties\DTOs\FiltersDTO;
use App\Application\Properties\DTOs\MarketSnapshotDTO;
use App\Application\Properties\DTOs\PropertyDTO;
use App\Application\Properties\DTOs\SourceDTO;
use App\Application\Properties\DTOs\SpyHuntDataDTO;
use App\Application\Properties\DTOs\StatsDTO;
use App\Application\Properties\DTOs\ValueEstimateDTO;
use App\Domain\Properties\Repositories\SpyHuntCacheRepositoryInterface;
use Illuminate\Support\Facades\Redis;

class RedisSpyHuntCacheRepository implements SpyHuntCacheRepositoryInterface
{
    private function key(int $propertyId): string
    {
        return "spyhunt:property:{$propertyId}";
    }

    /**
     * @throws \JsonException
     */
    public function get(int $propertyId, $filters = []): ?SpyHuntDataDTO
    {
        $raw = Redis::get($this->key($propertyId));

        if (!$raw) {
            return null;
        }
        $decoded = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);

        return new SpyHuntDataDTO(
            property: PropertyDTO::fromArray($decoded['property']),
            filters: FiltersDTO::defaults($decoded['filters']),
            marketSnapshot: MarketSnapshotDTO::fromArray(['sale'=> $decoded['comps']['sale'] ?? [], 'rent'=> $decoded['comps']['rent']]),
            valueEstimate: ValueEstimateDTO::fromArray($decoded['value_estimate']),
            comparables: ComparableDTO::fromArray($decoded['comps']['sale'],  $decoded['comps']['rent']),
            stats: StatsDTO::fromArray($decoded['stats']),
            source: SourceDTO::fromArray($decoded['source'])
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