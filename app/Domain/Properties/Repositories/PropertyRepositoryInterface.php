<?php

namespace App\Domain\Properties\Repositories;

use App\Domain\Properties\Entities\PropertyEntity;

interface PropertyRepositoryInterface
{
    public function save(PropertyEntity $propertyEntity): PropertyEntity;

    public function findById(int $id): ?PropertyEntity;

    public function findOrFail(int $id): PropertyEntity;
}