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
use App\Models\SpyHuntCache as SpyHuntCacheModel;
use Illuminate\Support\Carbon;
use App\Application\Properties\SpyHunt\Contracts\SpyHuntCache;

class EloquentSpyHuntRepository implements SpyHuntCache
{

    public function get(int $propertyId, $filters =[]): ?SpyHuntDataDTO
    {
       $row = SpyHuntCacheModel::query()
           ->where('property_id', $propertyId)
           ->first();
       if (! $row) {
           return null;
       }

        // TTL: si expiró, se considera cache miss ? no creo porque si ya exite en DB no tiene caso volver hacer
        // la peticion al provider externo por el momento se descarta
//        if ($row->expires_at !== null && Carbon::parse($row->expires_at)->isPast()) {
//            $row->delete();
//            return null;
//        }

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

    public function put(int $propertyId, mixed $data, int $ttlSeconds): void
    {
        SpyHuntCacheModel::query()->updateOrCreate(
            ['property_id' => $propertyId],
            [
                'payload' => $data->toArray(),
                'source' => $data->source->source,
                'last_synced_at' => now(),
                'expires_at' => Carbon::now()->addSeconds($ttlSeconds),
            ]
        );
    }

    public function forget(int $propertyId): void
    {
        SpyHuntCacheModel::query()
            ->where('property_id', $propertyId)
            ->delete();
    }
}