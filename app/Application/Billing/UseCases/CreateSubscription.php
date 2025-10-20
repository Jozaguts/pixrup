<?php

namespace App\Application\Billing\UseCases;

use App\Domain\Billing\Repositories\BillingRepositoryInterface;

class CreateSubscription
{
    public function __construct(
    protected BillingRepositoryInterface $repository
    ) {}

    public function execute(array $data): mixed
{
    // TODO: Implement business logic here.
    // Example:
    // return $this->repository->create($data);

    return 'UseCase CreateSubscription executed successfully.';
}
}
