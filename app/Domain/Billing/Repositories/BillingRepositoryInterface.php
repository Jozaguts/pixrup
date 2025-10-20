<?php

namespace App\Domain\Billing\Repositories;

interface BillingRepositoryInterface
{
    public function create(array $data);
    public function findById(int $id);
    public function update(int $id, array $data);
    public function delete(int $id);
}