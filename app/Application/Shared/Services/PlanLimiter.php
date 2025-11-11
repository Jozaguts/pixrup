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
        $plan = strtolower((string) ($user->plan ?? 'free'));

        $matrix = [
            'free' => [
                'appraisal.fetch' => 5,
                'glowup.generate' => 3,
                'property.create' => 1,
            ],
            'micro' => [
                'appraisal.fetch' => 8,
                'glowup.generate' => 5,
                'property.create' => 5,
            ],
            'starter' => [
                'appraisal.fetch' => 15,
                'glowup.generate' => 12,
                'property.create' => 12,
            ],
            'pro' => [
                'appraisal.fetch' => 60,
                'glowup.generate' => 40,
                'property.create' => 25,
            ],
            'enterprise' => [
                'appraisal.fetch' => null,
                'glowup.generate' => null,
                'property.create' => null,
            ],
        ];

        $planLimits = $matrix[$plan] ?? $matrix['free'];

        return $planLimits[$feature] ?? null;
    }

    /**
     * Description: Determine when a user's usage counter should reset for a feature.
     * Parameters: string $feature Feature identifier whose reset cadence is required.
     * Returns: ?string
     * Expected Result: Provides a valid DateInterval specification string describing reset frequency or null when it never resets.
     */
    public function getResetIntervalForFeature(string $feature): ?string
    {
        $intervals = [
            'appraisal.fetch' => 'P1M',
            'glowup.generate' => 'P1M',
            'property.create' => null,
        ];

        return $intervals[$feature] ?? 'P1M';
    }
}
