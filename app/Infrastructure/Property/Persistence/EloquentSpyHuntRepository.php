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
use App\Domain\Properties\Repositories\ICacheStore;
use App\Models\SpyHuntCache;
use Illuminate\Support\Carbon;

class EloquentSpyHuntRepository implements ICacheStore
{

    public function get(int $propertyId, $filters =[]): ?SpyHuntDataDTO
    {
       $row = SpyHuntCache::where('property_id', $propertyId)->first();
       if (! $row) {
           return null;
       }
       $payload = $row->payload;
        return new SpyHuntDataDTO(
            property: PropertyDTO::fromArray($payload['property']),
            filters: FiltersDTO::defaults($payload['filters']),
            marketSnapshot: MarketSnapshotDTO::fromArray(['sale'=> $payload['comps']['sale'] ?? [], 'rent'=> $payload['comps']['rent']]),
            valueEstimate: ValueEstimateDTO::fromArray($payload['value_estimate']),
            comparables: ComparableDTO::fromArray($payload['comps']['sale'],  $payload['comps']['rent']),
            stats: StatsDTO::fromArray($payload['stats']),
            source: SourceDTO::fromArray($payload['source'])
        );
    }

    public function put(int $propertyId, mixed $data, int $ttlInSeconds): void
    {
        SpyHuntCache::updateOrCreate(
            ['property_id' => $propertyId],
            [
                'payload' => $data->toArray(),
                'source' => $data->source->source,
                'last_synced_at' => now(),
                'expires_at' => Carbon::now()->addSeconds($ttlInSeconds),
            ]
        );
    }

    public function forget(int $propertyId): void
    {
       SpyHuntCache::where('property_id', $propertyId)->delete();
    }
}