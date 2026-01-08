<?php

namespace App\Application\Usage\Services;

use App\Domain\Usage\ValueObjects\PlanInfo;
use App\Models\User;

class PlanResolver
{
    public function resolve(User $user): PlanInfo
    {
        $configuredTiers = (array) config('plans.tiers', []);
        $defaultTier = (string) config('plans.default', 'PRICE_STARTER');

        // Source of truth: plan_tier (new)
        $rawTier = strtoupper((string) ($user->plan_tier ?: $defaultTier));

        // Allow friendly aliases if you ever pass "pro", "starter", etc.
        $aliases = (array) config('plans.aliases', []);
        $normalized = $aliases[strtolower($rawTier)] ?? $rawTier;

        if (! array_key_exists($normalized, $configuredTiers)) {
            $normalized = $defaultTier;
        }

        $tier = (array) ($configuredTiers[$normalized] ?? []);

        // Limits by bucket (KISS): -1 unlimited, 0 blocked, >0 quota/month
        $limits = (array) ($tier['limits'] ?? []);
        $docsLimit = array_key_exists('docs', $limits) ? (int) $limits['docs'] : -1;
        $rendersLimit = array_key_exists('renders', $limits) ? (int) $limits['renders'] : -1;

        return new PlanInfo(
            tier: $normalized,
            label: (string) ($tier['label'] ?? $normalized),
            limits: [
                'docs' => $docsLimit,
                'renders' => $rendersLimit,
            ],
        );
    }
}
