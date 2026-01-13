<?php

namespace App\Observers;

use App\Application\Usage\Services\PlanResolver;
use App\Domain\Usage\ValueObjects\UsageSummary;
use App\Models\UsagePropertyMonthly;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

readonly class UsagePropertyMonthlyObserver
{
    public function __construct(private PlanResolver $planResolver) {

    }
    public function created(UsagePropertyMonthly $model): void
    {
        $user = $model->user;
        if (! $user) {
            return;
        }

        $startDate = Carbon::now('UTC')->startOfMonth();
        $endDate = Carbon::now('UTC')->endOfMonth();
        $resetsAt = $startDate->copy()->addMonth()->startOfDay();
        $plan = $this->planResolver->resolve($user);

        $ttl       = $endDate->copy()->addDay();

        $docsLimit = $plan->limitForBucket('docs');
        $rendersLimit = $plan->limitForBucket('renders');
        $usedDocs = (int) $user->used_docs;
        $usedRenders = (int) $user->used_renders;

        $cacheKey = generateMonthlyUsageCacheKey($user->getKey(), $startDate->format('Y-m'));

        $summary = new UsageSummary(
            tier: $plan->tier,
            planLabel: $plan->label,
            periodKey: $startDate->format('Y-m'),
            resetsAt: $resetsAt,
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

        $payload = $summary->toArray();
        $payload['limitExceeded'] = $this->limitExceeded($payload['usage'] ?? []);

        Cache::put($cacheKey, $payload, $ttl);
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

    private function limitExceeded(array $usage): bool
    {
        foreach ($usage as $bucket) {
            if (! is_array($bucket)) {
                continue;
            }

            $limit = (int) ($bucket['limit'] ?? 0);
            $used = (int) ($bucket['used'] ?? 0);

            if ($limit > 0 && $used >= $limit) {
                return true;
            }
        }

        return false;
    }
}
