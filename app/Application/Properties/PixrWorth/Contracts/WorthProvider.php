<?php

namespace App\Application\Properties\PixrWorth\Contracts;

use App\Application\Properties\DTOs\PropertyWorthDTO;
use App\Domain\Properties\Entities\PropertyEntity;

interface WorthProvider
{
    public function appraisal(PropertyEntity $property): PropertyWorthDTO;
}