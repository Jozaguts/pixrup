<?php

namespace App\Application\Usage\Contracts;

use App\Domain\Properties\Entities\PropertyEntity;
use App\Domain\Usage\Enums\UsageAction;
use App\Domain\Usage\ValueObjects\UsagePeriod;
use App\Domain\Usage\ValueObjects\UsageScope;
use App\Models\User;

interface UsageEventLogger
{
    /**
     * @param array<string, mixed> $context
     */
    public function log(
        string $outcome,
        UsageScope $scope,
        User $user,
        PropertyEntity $property,
        UsageAction $action,
        UsagePeriod $period,
        array $context = [],
    ): void;
}
