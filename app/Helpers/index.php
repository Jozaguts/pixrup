<?php

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
