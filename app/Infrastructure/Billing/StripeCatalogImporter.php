<?php

namespace App\Infrastructure\Billing;

use App\Infrastructure\Billing\Handlers\UpsertStripePrice;
use App\Infrastructure\Billing\Handlers\UpsertStripeProduct;
use Illuminate\Database\Eloquent\Model;

final class StripeCatalogImporter
{
    public function __construct(
        private readonly StripeCatalogClient $client,
        private readonly UpsertStripeProduct $upsertProduct,
        private readonly UpsertStripePrice $upsertPrice,
    ) {}

    /** @return array<string, int> */
    public function importAll(): array
    {
        $counts = $this->initializeCounts();
        $startingAfter = null;

        do {
            $response = $this->client->listPricesWithProducts(100, $startingAfter);
            $prices = $response->data ?? [];

            foreach ($prices as $price) {
                $productPayload = data_get($price, 'product');
                if (is_string($productPayload) && $productPayload !== '') {
                    $productPayload = $this->client->retrieveProduct($productPayload);
                }
                if (is_array($productPayload) || is_object($productPayload)) {
                    $product = $this->upsertProduct->handle($productPayload);
                    $this->recordCounts($product, 'products', $counts);
                }

                $priceModel = $this->upsertPrice->handle($price);
                $this->recordCounts($priceModel, 'prices', $counts);
            }

            $last = end($prices);
            $startingAfter = is_object($last) ? ($last->id ?? null) : null;
        } while (!empty($response->has_more));

        return $counts;
    }

    /** @return array<string, int> */
    public function importProduct(string $stripeProductId): array
    {
        $counts = $this->initializeCounts();

        $product = $this->client->retrieveProduct($stripeProductId);
        $productModel = $this->upsertProduct->handle($product);
        $this->recordCounts($productModel, 'products', $counts);

        $startingAfter = null;
        do {
            $response = $this->client->listPricesForProduct($stripeProductId, 100, $startingAfter);
            $prices = $response->data ?? [];

            foreach ($prices as $price) {
                $priceModel = $this->upsertPrice->handle($price);
                $this->recordCounts($priceModel, 'prices', $counts);
            }

            $last = end($prices);
            $startingAfter = is_object($last) ? ($last->id ?? null) : null;
        } while (!empty($response->has_more));

        return $counts;
    }

    /** @return array<string, int> */
    private function initializeCounts(): array
    {
        return [
            'products_created' => 0,
            'products_updated' => 0,
            'prices_created' => 0,
            'prices_updated' => 0,
        ];
    }

    /** @param array<string, int> $counts */
    private function recordCounts(Model $model, string $prefix, array &$counts): void
    {
        if ($model->wasRecentlyCreated) {
            $counts[$prefix.'_created']++;
            return;
        }

        if ($model->wasChanged()) {
            $counts[$prefix.'_updated']++;
        }
    }
}
