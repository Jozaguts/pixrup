<?php

namespace App\Infrastructure\Billing\Handlers;

use App\Models\BillingProduct;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

final class UpsertStripeProduct
{
    public function handle(array|object $product): BillingProduct
    {
        $stripeProductId = (string) data_get($product, 'id', '');
        $existing = $stripeProductId
            ? BillingProduct::query()->where('stripe_product_id', $stripeProductId)->first()
            : null;

        $metadata = (array) data_get($product, 'metadata', []);
        $providedKey = Arr::get($metadata, 'key');
        $name = (string) data_get($product, 'name', $existing?->name ?? 'Unknown');
        $description = data_get($product, 'description', $existing?->description);
        $stripeDefaultPriceId = $this->normalizeDefaultPriceId($product)
            ?? $existing?->stripe_default_price_id;
        $isDeleted = (bool) data_get($product, 'deleted', false);
        $isActive = $isDeleted ? false : (bool) data_get($product, 'active', true);

        $type = $existing?->type ?? $this->normalizeType(Arr::get($metadata, 'type'));
        $key = $this->resolveKey($existing, $providedKey, $name, $stripeProductId);

        $values = [
            'key' => $key,
            'name' => $name ?: 'Unknown',
            'description' => $description,
            'type' => $type,
            'is_active' => $isActive,
            'sort_order' => $existing?->sort_order ?? 0,
            'stripe_default_price_id' => $stripeDefaultPriceId,
            'metadata' => !empty($metadata) ? $metadata : $existing?->metadata,
        ];

        $billingProduct = BillingProduct::query()->updateOrCreate(
            ['stripe_product_id' => $stripeProductId],
            $values
        );

        if (!$billingProduct->primary_price_id && $stripeDefaultPriceId) {
            $billingProduct->primary_price_id = $stripeDefaultPriceId;
            $billingProduct->save();
        }

        return $billingProduct;
    }

    private function normalizeType(?string $type): string
    {
        return $type === 'one_time' ? 'one_time' : 'subscription';
    }

    private function normalizeDefaultPriceId(array|object $product): ?string
    {
        $defaultPrice = data_get($product, 'default_price');
        if (is_array($defaultPrice)) {
            return $defaultPrice['id'] ?? null;
        }

        if (is_object($defaultPrice)) {
            return $defaultPrice->id ?? null;
        }

        if (is_string($defaultPrice) && $defaultPrice !== '') {
            return $defaultPrice;
        }

        return null;
    }

    private function resolveKey(?BillingProduct $existing, ?string $providedKey, string $name, string $stripeProductId): string
    {
        if ($existing?->key) {
            return $existing->key;
        }

        $base = $providedKey ?: Str::slug($name);
        if ($base === '') {
            $base = 'product';
        }

        $candidate = $base;
        if ($stripeProductId !== '') {
            $conflictQuery = BillingProduct::query()->where('key', $candidate);
            if ($existing) {
                $conflictQuery->where('id', '!=', $existing->getKey());
            }
            $conflict = $conflictQuery->exists();

            if ($conflict) {
                $candidate = $base.'-'.Str::slug($stripeProductId);
            }
        }

        return $candidate;
    }
}
