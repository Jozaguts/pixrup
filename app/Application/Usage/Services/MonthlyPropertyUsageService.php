<?php

namespace App\Application\Usage\Services;

use App\Domain\Shared\Exceptions\FeatureLimitExceededException;
use App\Domain\Usage\Enums\UsageAction;
use App\Domain\Usage\ValueObjects\PlanInfo;
use App\Domain\Usage\ValueObjects\UsagePeriod;
use App\Domain\Usage\ValueObjects\UsageScope;
use App\Models\Property;
use App\Models\UsagePropertyMonthly;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MonthlyPropertyUsageService
{
    public function __construct(
        private readonly PlanResolver $planResolver,
        private readonly UsageScopeResolver $scopeResolver,
        private readonly UsagePeriodService $periodService,
    ) {
    }

    /**
     * @throws FeatureLimitExceededException
     */
    public function ensureUsage(User $user, Property $property, UsageAction $action): void
    {
        $period = $this->periodService->current();
        $scope = $this->scopeResolver->resolve($user);
        $plan = $this->planResolver->resolve($user);

        DB::transaction(function () use ($user, $property, $action, $period, $scope, $plan): void {
            $lockedUser = User::query()
                ->whereKey($user->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            $this->refreshWindow($lockedUser, $period);

            $existing = UsagePropertyMonthly::query()
                ->where('account_scope_type', $scope->type)
                ->where('account_scope_id', $scope->id)
                ->where('property_id', $property->getKey())
                ->where('period_key', $period->key)
                ->first();

            if ($existing !== null) {
                $this->logOutcome('already_counted', $scope, $user, $property, $action, $period);

                return;
            }

            if ($plan->limit !== null && $lockedUser->usage_count >= $plan->limit) {
                $this->logOutcome('blocked', $scope, $user, $property, $action, $period, [
                    'limit' => $plan->limit,
                    'used' => $lockedUser->usage_count,
                ]);

                throw $this->limitException($plan, $lockedUser->usage_count, $period);
            }

            try {
                UsagePropertyMonthly::query()->create([
                    'account_scope_type' => $scope->type,
                    'account_scope_id' => $scope->id,
                    'user_id' => $user->getKey(),
                    'property_id' => $property->getKey(),
                    'period_key' => $period->key,
                    'plan_snapshot' => $plan->tier,
                    'action_first' => $action->value,
                    'first_action_at' => CarbonImmutable::now('UTC'),
                ]);
            } catch (QueryException $exception) {
                if ($exception->getCode() !== '23000') {
                    throw $exception;
                }

                $this->logOutcome('already_counted', $scope, $user, $property, $action, $period);

                return;
            }

            $lockedUser->usage_count = (int) $lockedUser->usage_count + 1;
            $lockedUser->usage_reset_at = $period->resetsAt->toDateTimeString();
            $lockedUser->save();

            $this->logOutcome('counted', $scope, $user, $property, $action, $period, [
                'used' => $lockedUser->usage_count,
                'limit' => $plan->limit,
            ]);
        });
    }

    private function refreshWindow(User $user, UsagePeriod $period): void
    {
        $resetAt = $user->usage_reset_at
            ? CarbonImmutable::parse($user->usage_reset_at, 'UTC')
            : null;

        if ($resetAt === null || $resetAt->ne($period->resetsAt)) {
            $currentCount = UsagePropertyMonthly::query()
                ->where('account_scope_type', 'user')
                ->where('account_scope_id', $user->getKey())
                ->where('period_key', $period->key)
                ->count();

            $user->usage_count = $currentCount;
            $user->usage_reset_at = $period->resetsAt->toDateTimeString();
        }
    }

    private function logOutcome(
        string $outcome,
        UsageScope $scope,
        User $user,
        Property $property,
        UsageAction $action,
        UsagePeriod $period,
        array $context = [],
    ): void {
        Log::channel('usage')->info('usage.event', [
            'outcome' => $outcome,
            'scope_type' => $scope->type,
            'scope_id' => $scope->id,
            'user_id' => $user->getKey(),
            'property_id' => $property->getKey(),
            'action' => $action->value,
            'period_key' => $period->key,
            'context' => $context,
        ]);
    }

    private function limitException(PlanInfo $plan, int $used, UsagePeriod $period): FeatureLimitExceededException
    {
        return new FeatureLimitExceededException(
            'You have reached your monthly property usage limit.',
            [
                'plan' => [
                    'tier' => $plan->tier,
                    'label' => $plan->label,
                    'limit' => $plan->limit,
                ],
                'used' => $used,
                'remaining' => $plan->limit !== null ? max(0, $plan->limit - $used) : null,
                'period_key' => $period->key,
                'resets_at' => $period->resetsAt->toIso8601String(),
            ],
        );
    }
}
