<?php

namespace App\Infrastructure\Billing\Handlers;

use App\Models\BillingPrice;
use App\Models\BillingProduct;
use Carbon\CarbonImmutable;
use Illuminate\Support\Str;

final class UpsertStripePrice
{
    public function handle(array|object $price): BillingPrice
    {
        $stripePriceId = (string) data_get($price, 'id', '');
        $stripeProductId = $this->resolveStripeProductId($price);

        $product = $this->resolveProduct($stripeProductId, $price);
        $isDeleted = (bool) data_get($price, 'deleted', false);
        $active = $isDeleted ? false : (bool) data_get($price, 'active', true);
        $type = data_get($price, 'type') === 'recurring' ? 'recurring' : 'one_time';
        $currency = strtoupper((string) data_get($price, 'currency', 'usd'));
        $unitAmount = (int) data_get($price, 'unit_amount', 0);

        $stripeCreatedAt = $this->normalizeStripeTimestamp(data_get($price, 'created'));
        $raw = $this->normalizeRaw($price);

        return BillingPrice::query()->updateOrCreate(
            ['stripe_price_id' => $stripePriceId],
            [
                'billing_product_id' => $product->id,
                'stripe_product_id' => $stripeProductId,
                'type' => $type,
                'currency' => $currency ?: 'USD',
                'unit_amount' => $unitAmount,
                'active' => $active,
                'interval' => data_get($price, 'recurring.interval'),
                'interval_count' => data_get($price, 'recurring.interval_count'),
                'trial_period_days' => data_get($price, 'recurring.trial_period_days'),
                'usage_type' => data_get($price, 'recurring.usage_type'),
                'billing_scheme' => data_get($price, 'billing_scheme'),
                'tax_behavior' => data_get($price, 'tax_behavior'),
                'stripe_created_at' => $stripeCreatedAt,
                'raw' => $raw,
            ]
        );
    }

    private function resolveStripeProductId(array|object $price): string
    {
        return (string) (data_get($price, 'product.id')
            ?? data_get($price, 'product')
            ?? '');
    }

    private function resolveProduct(string $stripeProductId, array|object $price): BillingProduct
    {
        $existing = BillingProduct::query()->where('stripe_product_id', $stripeProductId)->first();
        if ($existing) {
            return $existing;
        }

        $fallbackKey = $stripeProductId !== ''
            ? 'unknown-'.Str::slug($stripeProductId)
            : 'unknown-product';
        $type = data_get($price, 'type') === 'recurring' ? 'subscription' : 'one_time';

        return BillingProduct::query()->create([
            'key' => $fallbackKey,
            'name' => 'Unknown',
            'description' => null,
            'type' => $type,
            'is_active' => false,
            'sort_order' => 0,
            'stripe_product_id' => $stripeProductId !== '' ? $stripeProductId : null,
            'primary_price_id' => null,
            'stripe_default_price_id' => null,
            'metadata' => null,
        ]);
    }

    private function normalizeStripeTimestamp(mixed $timestamp): ?CarbonImmutable
    {
        if (!$timestamp) {
            return null;
        }

        return CarbonImmutable::createFromTimestamp((int) $timestamp);
    }

    private function normalizeRaw(array|object $payload): array
    {
        if (is_array($payload)) {
            return $payload;
        }

        return json_decode(json_encode($payload), true) ?: [];
    }
}
