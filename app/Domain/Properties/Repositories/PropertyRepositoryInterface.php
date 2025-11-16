<?php

namespace App\Domain\Properties\Repositories;

use App\Domain\Properties\Entities\PropertyEntity;

interface PropertyRepositoryInterface
{
    public function save(PropertyEntity $property): PropertyEntity;
}