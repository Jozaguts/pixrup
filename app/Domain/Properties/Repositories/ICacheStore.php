<?php

namespace App\Domain\Properties\Repositories;

interface ICacheStore
{
    public function get(int $propertyId, array $filters): mixed;

    public function put(int $propertyId, mixed $data,  int $ttlInSeconds): void;

    public function forget(int $propertyId): void;
}