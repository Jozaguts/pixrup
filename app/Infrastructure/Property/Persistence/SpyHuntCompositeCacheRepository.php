<?php

namespace App\Infrastructure\Property\Persistence;

use App\Application\Properties\DTOs\SpyHuntDataDTO;
use App\Domain\Properties\Repositories\SpyHuntCacheRepositoryInterface;

readonly class SpyHuntCompositeCacheRepository implements SpyHuntCacheRepositoryInterface
{
    public function __construct(
        private RedisSpyHuntCacheRepository $redis,
        private SpyHuntCacheRepository $eloquent
    ){}

    /**
     * @throws \JsonException
     */
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

    public function put(int $propertyId, SpyHuntDataDTO $data, int $ttlInSeconds = 86400): void
    {
        $this->redis->put($propertyId,$data, $ttlInSeconds);
        $this->eloquent->put($propertyId, $data, $ttlInSeconds);
    }

    public function forget(int $propertyId): void
    {
        $this->redis->forget($propertyId);
        $this->eloquent->forget($propertyId);
    }
}