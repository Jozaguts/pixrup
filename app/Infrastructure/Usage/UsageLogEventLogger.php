<?php

namespace App\Infrastructure\Usage;

use App\Application\Usage\Contracts\UsageEventLogger;
use App\Domain\Properties\Entities\PropertyEntity;
use App\Domain\Usage\Enums\UsageAction;
use App\Domain\Usage\ValueObjects\UsagePeriod;
use App\Domain\Usage\ValueObjects\UsageScope;
use App\Models\User;
use Illuminate\Support\Facades\Log;

final class UsageLogEventLogger implements UsageEventLogger
{
    public function log(
        string $outcome,
        UsageScope $scope,
        User $user,
        PropertyEntity $property,
        UsageAction $action,
        UsagePeriod $period,
        array $context = [],
    ): void {
        Log::channel('usage')->info('usage.event', [
            'outcome' => $outcome,
            'scope_type' => $scope->type,
            'scope_id' => $scope->id,
            'user_id' => $user->getKey(),
            'property_id' => $property->id,
            'action' => $action->value,
            'period_key' => $period->key,
            'context' => $context,
        ]);
    }
}
