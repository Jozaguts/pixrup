<?php

namespace App\Application\Usage\Services;

use App\Domain\Usage\ValueObjects\PlanInfo;
use App\Models\User;

class PlanResolver
{
    public function resolve(User $user): PlanInfo
    {
        $configuredTiers = config('plans.tiers', []);
        $defaultTier = config('plans.default', 'professional');
        $rawPlan = strtolower((string) ($user->plan ?? $defaultTier));
        $aliases = config('plans.aliases', []);
        $normalized = $aliases[$rawPlan] ?? $rawPlan;

        if (! array_key_exists($normalized, $configuredTiers)) {
            $normalized = $defaultTier;
        }

        $tier = $configuredTiers[$normalized];

        return new PlanInfo(
            $normalized,
            $tier['label'] ?? ucfirst($normalized),
            $tier['limit'] ?? null,
        );
    }
}
