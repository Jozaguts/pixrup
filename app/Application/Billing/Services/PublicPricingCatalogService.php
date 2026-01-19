<?php

declare(strict_types=1);

namespace App\Application\Billing\Services;

use App\Models\BillingPrice;
use App\Models\BillingProduct;
use Illuminate\Support\Collection;

class PublicPricingCatalogService
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public function catalog(): array
    {
        $products = BillingProduct::query()
            ->where('is_active', true)
            ->where('type', 'subscription')
            ->with(['prices' => function ($query) {
                $query->where('active', true);
            }])
            ->get();

        return $this->sortProducts($products)
            ->map(fn (BillingProduct $product) => $this->mapProduct($product))
            ->values()
            ->all();
    }

    /**
     * @param Collection<int, BillingProduct> $products
     * @return Collection<int, BillingProduct>
     */
    private function sortProducts(Collection $products): Collection
    {
        return $products->sortBy(function (BillingProduct $product): array {
            return [
                $this->resolveOrderRank($product),
                (int) ($product->sort_order ?? 0),
                (string) $product->name,
            ];
        });
    }

    /**
     * @return array<string, mixed>
     */
    private function mapProduct(BillingProduct $product): array
    {
        $metadata = (array) ($product->metadata ?? []);
        $prices = $product->prices ?? collect();
        $monthly = $this->resolveIntervalPrice($prices, 'month');
        $yearly = $this->resolveIntervalPrice($prices, 'year');

        return [
            'id' => $product->getKey(),
            'key' => (string) $product->key,
            'name' => (string) $product->name,
            'description' => $product->description,
            'is_featured' => (bool) ($metadata['featured'] ?? false),
            'features' => $this->normalizeFeatures(
                $metadata['features'] ?? $metadata['highlights'] ?? null
            ),
            'prices' => [
                'month' => $this->pricePayload($monthly),
                'year' => $this->pricePayload($yearly),
            ],
        ];
    }

    /**
     * @param Collection<int, BillingPrice> $prices
     */
    private function resolveIntervalPrice(Collection $prices, string $interval): ?BillingPrice
    {
        return $prices
            ->filter(fn (BillingPrice $price) => $price->interval === $interval)
            ->sortBy('unit_amount')
            ->first();
    }

    /**
     * @return array<string, mixed>|null
     */
    private function pricePayload(?BillingPrice $price): ?array
    {
        if (! $price) {
            return null;
        }

        return [
            'id' => $price->stripe_price_id,
            'unit_amount' => (int) $price->unit_amount,
            'currency' => (string) $price->currency,
            'interval' => $price->interval,
            'interval_count' => $price->interval_count,
        ];
    }

    private function resolveOrderRank(BillingProduct $product): int
    {
        $label = strtolower(trim((string) ($product->key ?: $product->name)));

        if ($label !== '') {
            if (str_contains($label, 'micro')) {
                return 0;
            }

            if (str_contains($label, 'starter') || str_contains($label, 'started')) {
                return 1;
            }

            if (str_contains($label, 'pro')) {
                return 2;
            }

            if (str_contains($label, 'enterprise') || str_contains($label, 'enterprice')) {
                return 3;
            }
        }

        return 999;
    }

    /**
     * @return array<int, string>
     */
    private function normalizeFeatures(mixed $features): array
    {
        if (is_array($features)) {
            return array_values(array_filter($features, 'is_string'));
        }

        if (is_string($features)) {
            $lines = preg_split('/\r\n|\r|\n/', $features) ?: [];
            $trimmed = array_map('trim', $lines);

            return array_values(array_filter($trimmed, fn (string $line) => $line !== ''));
        }

        return [];
    }
}
