<?php

namespace App\Infrastructure\Property\SpyHunt\Persistence;

use App\Application\Properties\DTOs\ComparableDTO;
use App\Application\Properties\DTOs\FiltersDTO;
use App\Application\Properties\DTOs\MarketSnapshotDTO;
use App\Application\Properties\DTOs\PropertyDTO;
use App\Application\Properties\DTOs\SourceDTO;
use App\Application\Properties\DTOs\SpyHuntDataDTO;
use App\Application\Properties\DTOs\StatsDTO;
use App\Application\Properties\DTOs\ValueEstimateDTO;
use App\Application\Properties\SpyHunt\Contracts\SpyHuntCache;
use App\Application\Shared\Contracts\Cache\KeyValueStore;
use JsonException;

final readonly class RedisSpyHuntCache implements SpyHuntCache
{
    public function __construct(private KeyValueStore $store) {}
    private function key(int $propertyId): string
    {
        return "spyhunt:property:{$propertyId}";
    }

    /**
     * @throws JsonException
     */
    public function get(int $propertyId, $filters = []): ?SpyHuntDataDTO
    {
        $raw = $this->store->get($this->key($propertyId));

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

    /**
     * @throws JsonException
     */
    public function put(int $propertyId, mixed $data, int $ttlSeconds): void
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