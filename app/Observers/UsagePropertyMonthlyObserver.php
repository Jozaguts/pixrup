<?php

namespace App\Observers;

use App\Application\Usage\Services\PlanResolver;
use App\Models\UsagePropertyMonthly;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

readonly class UsagePropertyMonthlyObserver
{
    public function __construct(private PlanResolver $planResolver) {

    }
    public function created(UsagePropertyMonthly $model): void
    {
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();
        $plan = $this->planResolver->resolve($model->user);
        $user = $model->user;

        $ttl       = $endDate->copy()->addDay();

        $limit = (int) ($plan->limit ?? 0);
        $usage = (int) ($user?->usage_count ?? 0);

        $cacheKey = generateMonthlyUsageCacheKey($user->getKey(), $startDate->format('Y-m'));

        $payload = [
            'user_id'     => $user->getKey(),
            'plan_id'     => $plan->id ?? null,
            'plan_name'   => $plan->name ?? null,
            'limit'       => $limit,
            'usage'       => $usage,
            'remaining'   => max(0, $limit - $usage),
            'is_depleted' => $limit > 0 && $usage >= $limit,
            'period'      => $startDate->format('Y-m'),
        ];

        Cache::put($cacheKey, $payload, $ttl);
    }
}
