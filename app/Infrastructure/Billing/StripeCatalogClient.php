<?php

namespace App\Infrastructure\Billing;

use Laravel\Cashier\Cashier;
use Stripe\Collection;
use Stripe\Product;

final class StripeCatalogClient
{
    public function listPricesWithProducts(int $limit = 100, ?string $startingAfter = null): Collection
    {
        $params = [
            'limit' => $limit,
            'expand' => ['data.product'],
        ];

        if ($startingAfter) {
            $params['starting_after'] = $startingAfter;
        }

        return Cashier::stripe()->prices->all($params);
    }

    public function listPricesForProduct(string $productId, int $limit = 100, ?string $startingAfter = null): Collection
    {
        $params = [
            'product' => $productId,
            'limit' => $limit,
        ];

        if ($startingAfter) {
            $params['starting_after'] = $startingAfter;
        }

        return Cashier::stripe()->prices->all($params);
    }

    public function retrieveProduct(string $productId): Product
    {
        return Cashier::stripe()->products->retrieve($productId, [
            'expand' => ['default_price'],
        ]);
    }
}
