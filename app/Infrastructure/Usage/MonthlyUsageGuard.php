<?php

namespace App\Infrastructure\Usage;

use App\Application\Auth\Contracts\CurrentUserProvider;
use App\Application\Usage\Contracts\UsageGuard;
use App\Domain\Properties\Entities\PropertyEntity;
use App\Domain\Shared\Exceptions\FeatureLimitExceededException;
use App\Domain\Usage\Enums\UsageAction;
use Throwable;

final readonly class MonthlyUsageGuard implements UsageGuard
{
    public function __construct(
        private MonthlyPropertyUsageService $service,
        private CurrentUserProvider $currentUser,
    ) {}

    /**
     * @throws Throwable
     * @throws FeatureLimitExceededException
     */
    public function ensure(UsageAction $action, PropertyEntity $property): void
    {
        $user = $this->currentUser->user();
        if (!$user) {
            return;
        }

        $this->service->ensureUsage($user, $property, $action);
    }
}