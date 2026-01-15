<?php

declare(strict_types=1);

namespace App\Application\Billing\Services;

use App\Models\BillingPrice;
use App\Models\BillingProduct;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class BillingPlanService
{
    /**
     * @return array{
     *     plans: array<int, array<string, mixed>>,
     *     active_plan: ?array{name: string, renews_at: ?string, is_canceling: bool, ends_at: ?string}
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
                    $activePlan = [
                        'name' => (string) $plan['name'],
                        'renews_at' => null,
                        'is_canceling' => $isCanceling,
                        'ends_at' => $isCanceling && $subscription->ends_at
                            ? $subscription->ends_at->toDateString()
                            : null,
                    ];
                    break;
                }
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

        $user->newSubscription('default', $price->stripe_price_id)
            ->create($paymentMethod->id);
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

        $subscription->swap($price->stripe_price_id);
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
}
