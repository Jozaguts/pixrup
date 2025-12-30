<?php

namespace App\Infrastructure\Property\Overview\Persistence;

use App\Application\Properties\Overview\Contracts\MsaMarketPulseRepository;
use App\Application\Properties\Overview\DTOs\MsaMarketPulseDTO;

final readonly class CompositeMsaMarketPulseRepository implements MsaMarketPulseRepository
{
    public function __construct(
        private MsaMarketPulseRepository $redis,
        private MsaMarketPulseRepository $eloquent,
    ) {}

    public function get(string $msa): ?MsaMarketPulseDTO
    {
        $data = $this->redis->get($msa);
        if ($data) {
            return $data;
        }

        $data = $this->eloquent->get($msa);
        if ($data) {
            $this->redis->save($data);
        }

        return $data;
    }

    public function save(MsaMarketPulseDTO $dto): void
    {
        $this->redis->save($dto);
        $this->eloquent->save($dto);
    }

    public function forget(string $msa): void
    {
        $this->redis->forget($msa);
        $this->eloquent->forget($msa);
    }
}
