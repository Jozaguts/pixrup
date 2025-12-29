<?php

namespace App\Application\Properties\PixrWorth\Contracts;

use App\Application\Properties\DTOs\PropertyWorthDTO;

interface WorthRepository
{
    public function findFresh(int $propertyId, \DateTimeInterface $threshold): ?PropertyWorthDTO;
    public function save(int $propertyId, PropertyWorthDTO $dto): void;
}