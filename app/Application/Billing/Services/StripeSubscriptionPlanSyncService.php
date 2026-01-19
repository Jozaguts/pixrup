<?php

declare(strict_types=1);

namespace App\Application\Billing\Services;

use Carbon\CarbonImmutable;
use App\Domain\Billing\Enums\SubscriptionStatus;
use App\Models\BillingPrice;
use App\Models\User;
use Laravel\Cashier\Cashier;

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

        $stripeSubscriptionId = (string) ($subscription['id'] ?? '');
        $periodEnd = (int) ($subscription['current_period_end'] ?? 0);
        $this->storeSubscriptionPeriodEnd($stripeSubscriptionId, $periodEnd);

        $priceId = (string) data_get($subscription, 'items.data.0.price.id', '');
        if ($priceId === '') {
            return;
        }

        $this->syncUserForPrice($user, $priceId);
    }

    public function syncUserForPrice(User $user, string $stripePriceId): bool
    {
        $tier = $this->resolveTierForPrice($stripePriceId);
        if (! $tier) {
            return false;
        }

        $pendingTier = strtoupper((string) ($user->pending_plan_tier ?? ''));
        $pendingChangeAt = $user->pending_plan_change_at;
        $now = now();

        if (
            $pendingTier !== ''
            && $pendingChangeAt
            && $now->lt($pendingChangeAt)
            && $tier === $pendingTier
        ) {
            return true;
        }

        if ($user->plan_tier === $tier && $pendingTier === '') {
            return true;
        }

        $user->plan_tier = $tier;
        if ($pendingTier === '' || $tier !== $pendingTier || ($pendingChangeAt && $now->gte($pendingChangeAt))) {
            $user->pending_plan_tier = null;
            $user->pending_plan_change_at = null;
        }
        $user->save();

        return true;
    }

    public function tierForPrice(string $stripePriceId): ?string
    {
        return $this->resolveTierForPrice($stripePriceId);
    }

    private function isActiveStatus(?string $status): bool
    {
        $resolved = SubscriptionStatus::fromStripe($status);

        return $resolved?->isActive() ?? false;
    }

    private function setDefaultTier(User $user): void
    {
        $defaultTier = (string) config('plans.default', 'PRICE_STARTER');

        if ($defaultTier === '') {
            return;
        }

        if ($user->plan_tier === $defaultTier) {
            if ($user->pending_plan_tier || $user->pending_plan_change_at) {
                $user->pending_plan_tier = null;
                $user->pending_plan_change_at = null;
                $user->save();
            }

            return;
        }

        $user->plan_tier = $defaultTier;
        $user->pending_plan_tier = null;
        $user->pending_plan_change_at = null;
        $user->save();
    }

    private function storeSubscriptionPeriodEnd(string $stripeSubscriptionId, int $periodEnd): void
    {
        if ($stripeSubscriptionId === '' || $periodEnd <= 0) {
            return;
        }

        $subscriptionModel = Cashier::$subscriptionModel;
        $subscription = $subscriptionModel::query()
            ->where('stripe_id', $stripeSubscriptionId)
            ->first();

        if (! $subscription) {
            return;
        }

        $subscription->forceFill([
            'current_period_ends_at' => CarbonImmutable::createFromTimestamp($periodEnd)->toDateTimeString(),
        ])->save();
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
