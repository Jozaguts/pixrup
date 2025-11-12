<?php

namespace App\Application\Usage\Services;

use App\Domain\Usage\ValueObjects\UsageSummary;
use App\Models\UsagePropertyMonthly;
use App\Models\User;

class UsageSummaryService
{
    public function __construct(
        private readonly PlanResolver $planResolver,
        private readonly UsagePeriodService $periodService,
    ) {
    }

    public function forUser(User $user): UsageSummary
    {
        $plan = $this->planResolver->resolve($user);
        $period = $this->periodService->current();

        $used = UsagePropertyMonthly::query()
            ->where('account_scope_type', 'user')
            ->where('account_scope_id', $user->getKey())
            ->where('period_key', $period->key)
            ->count();

        $remaining = $plan->limit !== null
            ? max(0, $plan->limit - $used)
            : null;

        return new UsageSummary(
            $plan->tier,
            $plan->label,
            $plan->limit,
            $used,
            $remaining,
            $period->key,
            $period->resetsAt,
        );
    }
}
