<?php

namespace App\Infrastructure\Property\Overview\Persistence;

use App\Application\Properties\Overview\Contracts\MsaMarketPulseRepository;
use App\Application\Properties\Overview\DTOs\MsaMarketPulseDTO;
use App\Application\Shared\Contracts\Cache\KeyValueStore;
use Carbon\CarbonImmutable;
use JsonException;

final readonly class RedisMsaMarketPulseCache implements MsaMarketPulseRepository
{
    public function __construct(private KeyValueStore $store) {}

    private function key(string $msa): string
    {
        return "overview:msa:{$msa}";
    }

    /**
     * @throws JsonException
     */
    public function get(string $msa): ?MsaMarketPulseDTO
    {
        $raw = $this->store->get($this->key($msa));
        if (!$raw) {
            return null;
        }

        $decoded = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
        return MsaMarketPulseDTO::fromArray($decoded);
    }

    /**
     * @throws JsonException
     */
    public function save(MsaMarketPulseDTO $dto): void
    {
        $this->store->put(
            $this->key($dto->msa),
            json_encode($dto->toArray(), JSON_THROW_ON_ERROR),
            $this->ttl($dto)
        );
    }

    public function forget(string $msa): void
    {
        $this->store->forget($this->key($msa));
    }

    private function ttl(MsaMarketPulseDTO $dto): int
    {
        $ttl = CarbonImmutable::now('UTC')->diffInSeconds($dto->expiresAt, false);
        return $ttl > 0 ? $ttl : 60;
    }
}
