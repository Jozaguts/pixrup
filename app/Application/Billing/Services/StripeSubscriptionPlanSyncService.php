<?php

declare(strict_types=1);

namespace App\Application\Billing\Services;

use App\Models\BillingPrice;
use App\Models\User;

class StripeSubscriptionPlanSyncService
{
    public function sync(array $payload): void
    {
        $subscription = data_get($payload, 'data.object');
        if (! is_array($subscription)) {
            return;
        }

        $customerId = (string) ($subscription['customer'] ?? '');
        if ($customerId === '') {
            return;
        }

        $user = User::query()->where('stripe_id', $customerId)->first();
        if (! $user) {
            return;
        }

        $status = (string) ($subscription['status'] ?? '');
        if (! $this->isActiveStatus($status)) {
            $this->setDefaultTier($user);

            return;
        }

        $priceId = (string) data_get($subscription, 'items.data.0.price.id', '');
        if ($priceId === '') {
            return;
        }

        $tier = $this->resolveTierForPrice($priceId);
        if (! $tier) {
            return;
        }

        if ($user->plan_tier !== $tier) {
            $user->plan_tier = $tier;
            $user->save();
        }
    }

    private function isActiveStatus(string $status): bool
    {
        return in_array($status, ['active', 'trialing'], true);
    }

    private function setDefaultTier(User $user): void
    {
        $defaultTier = (string) config('plans.default', 'PRICE_STARTER');

        if ($defaultTier === '' || $user->plan_tier === $defaultTier) {
            return;
        }

        $user->plan_tier = $defaultTier;
        $user->save();
    }

    private function resolveTierForPrice(string $stripePriceId): ?string
    {
        $price = BillingPrice::query()
            ->with('product')
            ->where('stripe_price_id', $stripePriceId)
            ->first();

        if (! $price || ! $price->product) {
            return null;
        }

        $productKey = (string) ($price->product->key ?? '');
        if ($productKey === 'pixrup-micro-use') {
            return null;
        }

        if ((string) $price->product->type !== 'subscription') {
            return null;
        }

        $tiers = (array) config('plans.tiers', []);
        $aliases = (array) config('plans.aliases', []);

        if ($productKey !== '') {
            $aliasKey = $aliases[strtolower($productKey)] ?? null;
            $normalized = $aliasKey ?? strtoupper($productKey);

            if (array_key_exists($normalized, $tiers)) {
                return $normalized;
            }
        }

        $metadata = (array) ($price->product->metadata ?? []);
        $metaTier = strtoupper((string) ($metadata['plan_tier'] ?? $metadata['tier'] ?? ''));
        if ($metaTier !== '' && array_key_exists($metaTier, $tiers)) {
            return $metaTier;
        }

        return null;
    }
}
