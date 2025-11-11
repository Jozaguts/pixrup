<?php

/**
 * Description: File providing the CheckFeatureLimit service to enforce subscription usage caps.
 * Parameters: None.
 * Returns: Void.
 * Expected Result: Enables application features to guard access based on plan thresholds.
 */

namespace App\Application\Shared\Services;

use App\Domain\Shared\Events\FeatureUsed;
use App\Domain\Shared\Exceptions\FeatureLimitExceededException;
use App\Models\User;
use Carbon\Carbon;
use DateInterval;
use Illuminate\Contracts\Cache\Repository as CacheRepository;

/**
 * Description: Service orchestrating feature limit checks and usage tracking for user plans.
 * Parameters: None.
 * Returns: Not applicable.
 * Expected Result: Guards protected features by throwing exceptions when limits are reached.
 */
class CheckFeatureLimit
{
    /**
     * Description: Construct the feature limit checker with its dependencies.
     * Parameters: PlanLimiter $planLimiter Resolves plan-specific limits; CacheRepository $cache Caches usage counters.
     * Returns: void.
     * Expected Result: Service initialized for validating and recording feature usage.
     */
    public function __construct(
        private readonly PlanLimiter $planLimiter,
        private readonly CacheRepository $cache,
    ) {
    }

    /**
     * Description: Ensure the given user has remaining usage for the target feature without mutating counters.
     * Parameters: User $user Account to validate; string $feature Feature identifier to inspect.
     * Returns: void.
     * Expected Result: Throws FeatureLimitExceededException when usage is exhausted.
     */
    public function assertUsageAvailable(User $user, string $feature): void
    {
        $limit = $this->planLimiter->getLimitForFeature($user, $feature);

        if ($limit === null) {
            return;
        }

        $usage = $this->getUsageState($user, $feature);

        if ($usage['count'] >= $limit) {
            throw new FeatureLimitExceededException(
                sprintf(
                    'Feature "%s" exhausted for user %d.',
                    $feature,
                    $user->getKey(),
                ),
            );
        }
    }

    /**
     * Description: Increment usage for the feature, persisting updated counters and reset schedule.
     * Parameters: User $user Account whose usage should increase; string $feature Feature identifier being consumed.
     * Returns: void.
     * Expected Result: Usage cache reflects the increment and expires at the scheduled reset time.
     */
    public function recordUsage(User $user, string $feature): void
    {
        $limit = $this->planLimiter->getLimitForFeature($user, $feature);
        if ($limit === null) {
            event(new FeatureUsed($user->getKey(), $feature, 1));
            return;
        }
        $resetInterval = $this->planLimiter->getResetIntervalForFeature($feature);
        $state = $this->getUsageState($user, $feature);

        ['count' => $count, 'reset_at' => $resetAt] = $state;

        if ($resetInterval !== null) {
            if ($resetAt === null || Carbon::parse($resetAt)->isPast()) {
                $resetAt = $this->makeResetAt($feature);
                $count = 0;
            }
        } else {
            $resetAt = null;
        }

        $count++;

        $payload = [
            'count' => $count,
            'reset_at' => $resetAt,
            'limit' => $limit,
        ];

        if ($resetInterval === null) {
            $this->cache->forever($this->cacheKey($user, $feature), $payload);
        } else {
            $ttl = max(1, Carbon::parse($resetAt)->diffInSeconds(now()));
            $this->cache->put(
                $this->cacheKey($user, $feature),
                $payload,
                $ttl
            );
        }

        event(new FeatureUsed($user->getKey(), $feature, 1));
    }

    /**
     * Description: Retrieve the cached usage record for the feature or bootstrap defaults.
     * Parameters: User $user Account to inspect; string $feature Feature identifier to inspect.
     * Returns: array{count:int,reset_at:?string,limit:?int}
     * Expected Result: Provides normalized usage state enabling limit calculations.
     */
    private function getUsageState(User $user, string $feature): array
    {
        $state = $this->cache->get($this->cacheKey($user, $feature), []);

        $count = (int) ($state['count'] ?? 0);
        $resetAt = $state['reset_at'] ?? null;
        $limit = $this->planLimiter->getLimitForFeature($user, $feature);
        $resetInterval = $this->planLimiter->getResetIntervalForFeature($feature);

        if ($resetInterval !== null && $resetAt !== null && Carbon::parse($resetAt)->isPast()) {
            $count = 0;
            $resetAt = null;
        }

        return [
            'count' => $count,
            'reset_at' => $resetAt,
            'limit' => $limit,
        ];
    }

    /**
     * Description: Generate the cache key used to store usage information.
     * Parameters: User $user Subject user; string $feature Feature identifier.
     * Returns: string
     * Expected Result: Ensures deterministic cache namespace per user-feature combination.
     */
    private function cacheKey(User $user, string $feature): string
    {
        return sprintf('feature-limit:%d:%s', $user->getKey(), $feature);
    }

    /**
     * Description: Calculate the reset timestamp for the provided feature respecting plan cadence.
     * Parameters: string $feature Feature identifier.
     * Returns: ?string
     * Expected Result: Returns ISO-8601 timestamp describing when usage resets or null for non-resettable features.
     */
    private function makeResetAt(string $feature): ?string
    {
        $spec = $this->planLimiter->getResetIntervalForFeature($feature);
        if ($spec === null) {
            return null;
        }
        $interval = new DateInterval($spec);

        return now()->add($interval)->toIso8601String();
    }

    /**
     * Description: Provide the current usage snapshot for the given user-feature pair.
     *
     * @return array{count:int,reset_at:?string,limit:?int}
     */
    public function usageSnapshot(User $user, string $feature): array
    {
        return $this->getUsageState($user, $feature);
    }
}
