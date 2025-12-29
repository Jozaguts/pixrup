<?php

namespace App\Application\Usage\Contracts;

use App\Domain\Usage\Enums\UsageAction;
use App\Domain\Properties\Entities\PropertyEntity;

interface UsageGuard
{
    public function ensure(UsageAction $action, PropertyEntity $property): void;
}
