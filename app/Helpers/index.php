<?php
use App\Models\User;

if(!function_exists('generateMonthlyUsageCacheKey')) {
    /**
     * Generate a cache key for monthly usage tracking.
     *
     * @param int|string $userId The ID of the user.
     * @param string $period The period in 'Y-m' format.
     * @return string The generated cache key.
     */
    function generateMonthlyUsageCacheKey(int|string $userId, string $period): string
    {
        return sprintf('user:%s:period:%s', $userId, $period);
    }
}

    /** Checks if the monthly usage limit has been exceeded. */
if(!function_exists('isMonthlyUsageLimitExceeded')) {
        function isMonthlyUsageLimitExceeded(?User $user= null, ?string $periodKey = null) : bool {
            if(!$user) {
                // guest users are always limited.
                return true;
            }
             $periodKey ??= now()->format('Y-m');
             $userKey = $user->getKey() ?? 'guest';
             $cacheKey = generateMonthlyUsageCacheKey($userKey, $periodKey);
             $cachePayload = cache()->get($cacheKey);
             return isset($cachePayload["limitExceeded"]) && !$cachePayload['limitExceeded'];
        }
    }
