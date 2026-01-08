<?php

namespace App\Infrastructure\Usage;

use App\Application\Usage\Services\PlanResolver;
use App\Application\Usage\Services\UsagePeriodService;
use App\Application\Usage\Services\UsageScopeResolver;
use App\Domain\Properties\Entities\PropertyEntity;
use App\Domain\Shared\Exceptions\FeatureLimitExceededException;
use App\Domain\Usage\Enums\UsageAction;
use App\Domain\Usage\ValueObjects\PlanInfo;
use App\Domain\Usage\ValueObjects\UsagePeriod;
use App\Domain\Usage\ValueObjects\UsageScope;
use App\Domain\Usage\ValueObjects\UsageSummary;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

readonly class MonthlyPropertyUsageService
{
    public function __construct(
        private PlanResolver $planResolver,
        private UsageScopeResolver $scopeResolver,
        private UsagePeriodService $periodService,
    ) {
    }

    /**
     * @throws FeatureLimitExceededException|Throwable
     */
    public function ensureUsage(User $user, PropertyEntity $property, UsageAction $action): void
    {
        $period = $this->periodService->current();
        $scope = $this->scopeResolver->resolve($user);
        $plan = $this->planResolver->resolve($user);
        $bucket = $plan->bucketForAction($action);

        DB::transaction(function () use ($user, $property, $action, $period, $scope, $plan, $bucket): void {
            $lockedUser = User::query()
                ->whereKey($user->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            $this->refreshWindow($lockedUser, $period);

            $used = $this->usedForBucket($lockedUser, $bucket);

            if (! $plan->allows($bucket, $used)) {
                $this->logOutcome('blocked', $scope, $user, $property, $action, $period, [
                    'bucket' => $bucket,
                    'limit' => $plan->limitForBucket($bucket),
                    'used' => $used,
                ]);

                throw $this->limitException($plan, $bucket, $lockedUser, $period);
            }

            $nextUsed = $this->incrementBucket($lockedUser, $bucket);
            $lockedUser->usage_reset_at = $period->resetsAt->toDateTimeString();
            $lockedUser->save();

            $this->logOutcome('counted', $scope, $user, $property, $action, $period, [
                'bucket' => $bucket,
                'used' => $nextUsed,
                'limit' => $plan->limitForBucket($bucket),
            ]);
        });
    }

    private function refreshWindow(User $user, UsagePeriod $period): void
    {
        $resetAt = $user->usage_reset_at
            ? CarbonImmutable::parse($user->usage_reset_at, 'UTC')
            : null;

        if ($resetAt === null || $resetAt->ne($period->resetsAt)) {
            $user->used_docs = 0;
            $user->used_renders = 0;
            $user->usage_reset_at = $period->resetsAt->toDateTimeString();
        }
    }

    private function usedForBucket(User $user, string $bucket): int
    {
        return $bucket === 'renders'
            ? (int) $user->used_renders
            : (int) $user->used_docs;
    }

    private function incrementBucket(User $user, string $bucket): int
    {
        if ($bucket === 'renders') {
            $user->used_renders = (int) $user->used_renders + 1;

            return (int) $user->used_renders;
        }

        $user->used_docs = (int) $user->used_docs + 1;

        return (int) $user->used_docs;
    }

    private function logOutcome(
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

    private function limitException(
        PlanInfo $plan,
        string $bucket,
        User $user,
        UsagePeriod $period
    ): FeatureLimitExceededException
    {
        return new FeatureLimitExceededException(
            'You have reached your monthly property usage limit.',
            $this->summaryPayload($plan, $user, $period, $bucket),
        );
    }

    private function summaryPayload(
        PlanInfo $plan,
        User $user,
        UsagePeriod $period,
        string $bucket
    ): array {
        $usedDocs = (int) $user->used_docs;
        $usedRenders = (int) $user->used_renders;
        $docsLimit = $plan->limitForBucket('docs');
        $rendersLimit = $plan->limitForBucket('renders');

        $summary = new UsageSummary(
            tier: $plan->tier,
            planLabel: $plan->label,
            periodKey: $period->key,
            resetsAt: $period->resetsAt,
            docs: [
                'limit' => $docsLimit,
                'used' => $usedDocs,
                'remaining' => $this->remaining($docsLimit, $usedDocs),
            ],
            renders: [
                'limit' => $rendersLimit,
                'used' => $usedRenders,
                'remaining' => $this->remaining($rendersLimit, $usedRenders),
            ],
        );

        return [
            'bucket' => $bucket,
            ...$summary->toArray(),
        ];
    }

    private function remaining(int $limit, int $used): ?int
    {
        if ($limit === -1) {
            return null;
        }

        if ($limit === 0) {
            return 0;
        }

        return max(0, $limit - $used);
    }
}
