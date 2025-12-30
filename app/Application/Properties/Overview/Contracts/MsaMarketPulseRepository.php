<?php

namespace App\Application\Properties\Overview\Contracts;

use App\Application\Properties\Overview\DTOs\MsaMarketPulseDTO;

interface MsaMarketPulseRepository
{
    public function get(string $msa): ?MsaMarketPulseDTO;
    public function save(MsaMarketPulseDTO $dto): void;
    public function forget(string $msa): void;
}
