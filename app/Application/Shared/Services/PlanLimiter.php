<?php

/**
 * Description: File implementing plan-based limit resolution utilities.
 * Parameters: None.
 * Returns: Void.
 * Expected Result: Provides access to feature usage limits derived from subscription plans.
 */

namespace App\Application\Shared\Services;

use App\Models\User;

/**
 * Description: Service responsible for resolving plan limits for specific application features.
 * Parameters: None.
 * Returns: Not applicable.
 * Expected Result: Supplies numeric usage caps enabling enforcement services to operate.
 */
class PlanLimiter
{
    /**
     * Description: Fetch the usage limit for a given user and feature combination.
     * Parameters: User $user Authenticated account subject to limits; string $feature Identifier for the feature being accessed.
     * Returns: ?int
     * Expected Result: Returns integer limit when enforced or null when unlimited.
     */
    public function getLimitForFeature(User $user, string $feature): ?int
    {
        $plan = $user->plan ?? 'free';

        $matrix = [
            'free' => [
                'appraisal.fetch' => 5,
            ],
            'starter' => [
                'appraisal.fetch' => 15,
            ],
            'pro' => [
                'appraisal.fetch' => 60,
            ],
            'enterprise' => [
                'appraisal.fetch' => null,
            ],
        ];

        $planLimits = $matrix[$plan] ?? $matrix['free'];

        return $planLimits[$feature] ?? null;
    }

    /**
     * Description: Determine when a user's usage counter should reset for a feature.
     * Parameters: string $feature Feature identifier whose reset cadence is required.
     * Returns: string
     * Expected Result: Provides a valid DateInterval specification string describing reset frequency.
     */
    public function getResetIntervalForFeature(string $feature): string
    {
        $intervals = [
            'appraisal.fetch' => 'P1M',
        ];

        return $intervals[$feature] ?? 'P1M';
    }
}
