<?php

namespace App\Infrastructure\Property\SpyHunt\Persistence;

use App\Application\Properties\DTOs\SpyHuntDataDTO;
use App\Application\Properties\SpyHunt\Contracts\SpyHuntCacheRepository;

readonly class CompositeSpyHuntCacheRepository implements SpyHuntCacheRepository
{
    public function __construct(
        private SpyHuntCacheRepository $redis,
        private SpyHuntCacheRepository $eloquent
    ){}

    public function get(int $propertyId, array $filters = []): ?SpyHuntDataDTO
    {
        $data = $this->redis->get($propertyId, $filters);

        if ($data){
            return $data;
        }
        $data = $this->eloquent->get($propertyId, $filters);

        if ($data){
            $this->redis->put($propertyId,$data, 86400);
            return $data;
        }

        return null;
    }

    public function put(int $propertyId, mixed $data, int $ttlSeconds = 86400): void
    {
        $this->redis->put($propertyId,$data, $ttlSeconds);
        $this->eloquent->put($propertyId, $data, $ttlSeconds);
    }

    public function forget(int $propertyId): void
    {
        $this->redis->forget($propertyId);
        $this->eloquent->forget($propertyId);
    }
}
