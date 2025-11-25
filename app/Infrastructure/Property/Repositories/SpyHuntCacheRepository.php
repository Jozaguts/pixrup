<?php

namespace App\Infrastructure\Property\Repositories;

use App\Application\Properties\DTOs\SpyHuntDataDTO;
use App\Domain\Properties\Repositories\SpyHuntCacheRepositoryInterface;
use App\Models\SpyHuntCache;
use Illuminate\Support\Carbon;

class SpyHuntCacheRepository implements SpyHuntCacheRepositoryInterface
{

    public function get(int $propertyId): ?SpyHuntDataDTO
    {
       $row = SpyHuntCache::where('property_id', $propertyId)->first();
       if (! $row) {
           return null;
       }
       if($row->expires_at && $row->expires_at->isPast()) {
           return null;
       }
       return new SpyHuntDataDTO(
           $row->payload['property'],
           $row->payload['filters'],
           $row->payload['market_snapshot'],
           $row->payload['value_estimate'],
           $row->payload['comparables'],
           $row->payload['stats'],
           $row->payload['source'],
       );
    }

    public function put(int $propertyId, SpyHuntDataDTO $data, int $ttlInSeconds): void
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