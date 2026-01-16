<?php

declare(strict_types=1);

namespace App\Application\Billing\Services;

use App\Application\Billing\Jobs\FinalizePendingPlanChangeJob;
use App\Application\Billing\Jobs\SyncSubscriptionPlanJob;
use App\Domain\Billing\Enums\SubscriptionStatus;
use App\Models\BillingPrice;
use App\Models\BillingProduct;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravel\Cashier\Subscription;

class BillingPlanService
{
    public function __construct(
        private readonly StripeSubscriptionPlanSyncService $planSync,
    ) {
    }

    /**
     * @return array{
     *     plans: array<int, array<string, mixed>>,
     *     active_plan: ?array{name: string, renews_at: ?string, is_canceling: bool, ends_at: ?string, pending_plan?: ?array{name: string, starts_at: ?string}}
     * }
     */
    public function catalog(User $user): array
    {
        $products = BillingProduct::query()
            ->where('is_active', true)
            ->where('type', 'subscription')
            ->with(['prices' => function ($query) {
                $query->where('active', true);
            }])
            ->orderBy('sort_order')
            ->get();

        $subscription = $user->subscription('default');
        $currentPriceId = $subscription?->stripe_price;
        $isSubscriptionActive = $subscription?->valid() ?? false;

        $plans = $products
            ->map(function (BillingProduct $product) use ($currentPriceId, $isSubscriptionActive): ?array {
                $price = $this->resolvePrimaryPrice($product, $product->prices);
                if (! $price) {
                    return null;
                }

                return [
                    'id' => $product->getKey(),
                    'key' => $product->key,
                    'name' => $product->name,
                    'description' => $product->description,
                    'price' => [
                        'id' => $price->stripe_price_id,
                        'unit_amount' => (int) $price->unit_amount,
                        'currency' => $price->currency,
                        'interval' => $price->interval,
                        'interval_count' => $price->interval_count,
                    ],
                    'is_current' => $isSubscriptionActive && $currentPriceId !== null
                        && $currentPriceId === $price->stripe_price_id,
                ];
            })
            ->filter()
            ->values()
            ->all();

        $activePlan = null;
        if ($subscription && $isSubscriptionActive && $currentPriceId) {
            foreach ($plans as $plan) {
                if (! empty($plan['is_current'])) {
                    $isCanceling = $subscription->onGracePeriod();
                    $renewsAt = $isCanceling ? null : $this->resolveRenewalDate($subscription);

                    $activePlan = [
                        'name' => (string) $plan['name'],
                        'renews_at' => $renewsAt,
                        'is_canceling' => $isCanceling,
                        'ends_at' => $isCanceling && $subscription->ends_at
                            ? $subscription->ends_at->toDateString()
                            : null,
                    ];
                    break;
                }
            }

            $pendingPlan = $this->resolvePendingPlanPayload($user);
            if ($pendingPlan !== null && $activePlan !== null) {
                $activePlan['pending_plan'] = $pendingPlan;
            }
        }

        return [
            'plans' => $plans,
            'active_plan' => $activePlan,
        ];
    }

    public function subscribe(User $user, string $stripePriceId): void
    {
        $price = $this->resolveActiveSubscriptionPrice($stripePriceId);

        if ($user->subscribed('default')) {
            $this->swap($user, $stripePriceId);

            return;
        }

        if (! $user->stripe_id) {
            $user->createAsStripeCustomer();
        }

        $paymentMethod = $user->defaultPaymentMethod();
        if (! $paymentMethod) {
            throw ValidationException::withMessages([
                'price_id' => 'Add a payment method before subscribing.',
            ]);
        }

        $subscription = $user->newSubscription('default', $price->stripe_price_id)
            ->create($paymentMethod->id);

        $this->syncPlanTierAfterSubscription($user, $subscription);
    }

    public function swap(User $user, string $stripePriceId): void
    {
        $price = $this->resolveActiveSubscriptionPrice($stripePriceId);
        $subscription = $user->subscription('default');

        if (! $subscription) {
            $this->subscribe($user, $stripePriceId);

            return;
        }

        if ($subscription->stripe_price === $price->stripe_price_id) {
            return;
        }

        $currentTier = $this->resolveCurrentTier($user, $subscription);
        $targetTier = $this->planSync->tierForPrice($price->stripe_price_id);
        $direction = $this->compareTierDirection($currentTier, $targetTier, $subscription, $price);

        if ($direction > 0) {
            $subscription->swapAndInvoice($price->stripe_price_id);
            $subscription->refresh();
            $this->clearPendingPlan($user);
            $this->syncPlanTierAfterSubscription($user, $subscription);

            return;
        }

        if ($direction < 0) {
            $subscription->noProrate()->swap($price->stripe_price_id);
            $subscription->refresh();
            $this->schedulePendingPlan($user, $subscription, $targetTier);

            return;
        }

        $subscription->swap($price->stripe_price_id);
        $subscription->refresh();
        $this->syncPlanTierAfterSubscription($user, $subscription);
    }

    public function cancel(User $user): void
    {
        $subscription = $user->subscription('default');
        if (! $subscription) {
            return;
        }

        if ($subscription->onGracePeriod()) {
            return;
        }

        $subscription->cancel();
        $this->clearPendingPlan($user);
    }

    private function resolveActiveSubscriptionPrice(string $stripePriceId): BillingPrice
    {
        $price = BillingPrice::query()
            ->where('stripe_price_id', $stripePriceId)
            ->where('active', true)
            ->whereHas('product', function ($query): void {
                $query->where('is_active', true)
                    ->where('type', 'subscription');
            })
            ->first();

        if (! $price) {
            throw ValidationException::withMessages([
                'price_id' => 'Selected plan is unavailable.',
            ]);
        }

        return $price;
    }

    /**
     * @param Collection<int, BillingPrice> $prices
     */
    private function resolvePrimaryPrice(BillingProduct $product, Collection $prices): ?BillingPrice
    {
        $preferred = $product->primary_price_id ?: $product->stripe_default_price_id;
        if ($preferred) {
            $match = $prices->firstWhere('stripe_price_id', $preferred);
            if ($match instanceof BillingPrice) {
                return $match;
            }
        }

        $sorted = $prices->sortBy('unit_amount')->values();
        $first = $sorted->first();

        return $first instanceof BillingPrice ? $first : null;
    }

    private function syncPlanTierAfterSubscription(User $user, ?Subscription $subscription): void
    {
        if (! $subscription) {
            return;
        }

        $this->syncCurrentPeriodEnd($subscription);

        $status = SubscriptionStatus::fromStripe($subscription->stripe_status);
        if ($status?->isActive()) {
            if ($subscription->stripe_price) {
                $this->planSync->syncUserForPrice($user, $subscription->stripe_price);
                return;
            }
        }

        if ($subscription->stripe_id) {
            SyncSubscriptionPlanJob::dispatch(
                (string) $user->getKey(),
                $subscription->stripe_id
            )->delay(now()->addMinute());
        }
    }

    private function resolveCurrentTier(User $user, Subscription $subscription): ?string
    {
        if ($subscription->stripe_price) {
            $tier = $this->planSync->tierForPrice($subscription->stripe_price);
            if ($tier) {
                return $tier;
            }
        }

        $fallback = (string) config('plans.default', 'PRICE_STARTER');
        $current = strtoupper((string) ($user->plan_tier ?: $fallback));

        return $current !== '' ? $current : null;
    }

    private function compareTierDirection(
        ?string $currentTier,
        ?string $targetTier,
        Subscription $subscription,
        BillingPrice $targetPrice,
    ): int {
        if ($currentTier && $targetTier) {
            $tiers = array_keys((array) config('plans.tiers', []));
            $currentRank = array_search($currentTier, $tiers, true);
            $targetRank = array_search($targetTier, $tiers, true);

            if ($currentRank !== false && $targetRank !== false) {
                return $targetRank <=> $currentRank;
            }
        }

        $currentPrice = null;
        if ($subscription->stripe_price) {
            $currentPrice = BillingPrice::query()
                ->where('stripe_price_id', $subscription->stripe_price)
                ->first();
        }

        if ($currentPrice && $currentPrice->unit_amount !== null) {
            return ((int) $targetPrice->unit_amount) <=> ((int) $currentPrice->unit_amount);
        }

        return 0;
    }

    private function schedulePendingPlan(User $user, Subscription $subscription, ?string $targetTier): void
    {
        if (! $targetTier) {
            return;
        }

        $changeAt = $this->resolvePeriodEndDate($subscription);
        if (! $changeAt) {
            return;
        }

        $user->pending_plan_tier = $targetTier;
        $user->pending_plan_change_at = $changeAt;
        $user->save();

        FinalizePendingPlanChangeJob::dispatch((string) $user->getKey())
            ->delay($changeAt);
    }

    private function clearPendingPlan(User $user): void
    {
        if (! $user->pending_plan_tier && ! $user->pending_plan_change_at) {
            return;
        }

        $user->pending_plan_tier = null;
        $user->pending_plan_change_at = null;
        $user->save();
    }

    private function resolveRenewalDate(Subscription $subscription): ?string
    {
        $periodEnd = $this->resolvePeriodEndDate($subscription);

        return $periodEnd?->toIso8601String();
    }

    private function resolvePeriodEndDate(Subscription $subscription): ?CarbonImmutable
    {
        $stored = $subscription->current_period_ends_at;
        if ($stored) {
            try {
                return CarbonImmutable::parse($stored);
            } catch (\Throwable $exception) {
                // Continue to Stripe fallback.
            }
        }

        try {
            $stripeSubscription = $subscription->asStripeSubscription();
            $periodEnd = (int) ($stripeSubscription->current_period_end ?? 0);
            if ($periodEnd > 0) {
                $date = CarbonImmutable::createFromTimestamp($periodEnd);
                $subscription->forceFill([
                    'current_period_ends_at' => $date->toDateTimeString(),
                ])->save();

                return $date;
            }
        } catch (\Throwable $exception) {
            return null;
        }

        return null;
    }

    private function syncCurrentPeriodEnd(Subscription $subscription): void
    {
        try {
            $stripeSubscription = $subscription->asStripeSubscription();
            $periodEnd = (int) ($stripeSubscription->current_period_end ?? 0);
            if ($periodEnd > 0) {
                $date = CarbonImmutable::createFromTimestamp($periodEnd);
                $subscription->forceFill([
                    'current_period_ends_at' => $date->toDateTimeString(),
                ])->save();
            }
        } catch (\Throwable $exception) {
            // Keep existing value if Stripe is unavailable.
        }
    }

    private function resolvePendingPlanPayload(User $user): ?array
    {
        $pendingTier = strtoupper((string) ($user->pending_plan_tier ?? ''));
        if ($pendingTier === '' || ! $user->pending_plan_change_at) {
            return null;
        }

        $tiers = (array) config('plans.tiers', []);
        $label = $tiers[$pendingTier]['label'] ?? Str::title(strtolower(str_replace('_', ' ', $pendingTier)));

        return [
            'name' => (string) $label,
            'starts_at' => $user->pending_plan_change_at?->toIso8601String(),
        ];
    }
}
