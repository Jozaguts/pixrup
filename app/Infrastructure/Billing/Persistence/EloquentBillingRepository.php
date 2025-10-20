<?php

namespace App\Infrastructure\Billing\Persistence;
use App\Domain\Billing\Repositories\BillingRepositoryInterface;
use App\Models\Subscription;

class EloquentBillingRepository implements  BillingRepositoryInterface
{
    public function create(array $data): ?Subscription
    {
        return Subscription::create($data);
    }

    public function findById(int $id): ?Subscription
    {
        return Subscription::findOrFail($id);
    }

    public function update(int $id, array $data): ?Subscription
    {
        $record = Subscription::findOrFail($id);
        $record->update($data);
        return $record;
    }

    public function delete(int $id): int
    {
        return Subscription::destroy($id);
    }
}