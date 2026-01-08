<?php

namespace App\Application\Usage\Services;

use App\Domain\Usage\ValueObjects\UsageSummary;
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

        $usedDocs = (int) $user->used_docs;
        $usedRenders = (int) $user->used_renders;

        $docsLimit = $plan->limitForBucket('docs');         // -1 unlimited, 0 blocked, >0 quota
        $rendersLimit = $plan->limitForBucket('renders');

        $remainingDocs = $this->remaining($docsLimit, $usedDocs);
        $remainingRenders = $this->remaining($rendersLimit, $usedRenders);

        return new UsageSummary(
            tier: $plan->tier,
            planLabel: $plan->label,
            periodKey: $period->key,
            resetsAt: $period->resetsAt,
            docs: [
                'limit' => $docsLimit,
                'used' => $usedDocs,
                'remaining' => $remainingDocs,
            ],
            renders: [
                'limit' => $rendersLimit,
                'used' => $usedRenders,
                'remaining' => $remainingRenders,
            ],
        );
    }

    /**
     * @return int|null  null = unlimited, 0..n = remaining
     */
    private function remaining(int $limit, int $used): ?int
    {
        if ($limit === -1) {
            return null; // unlimited
        }

        if ($limit === 0) {
            return 0; // blocked
        }

        return max(0, $limit - $used);
    }
}
