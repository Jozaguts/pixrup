<?php

declare(strict_types=1);

namespace App\Http\Controllers\Billing;

use App\Application\Billing\Services\StripeSubscriptionPlanSyncService;
use App\Infrastructure\Billing\Handlers\UpsertStripePrice;
use App\Infrastructure\Billing\Handlers\UpsertStripeProduct;
use Illuminate\Http\Request;
use Laravel\Cashier\Http\Controllers\WebhookController as CashierWebhookController;
use Symfony\Component\HttpFoundation\Response;

class StripeWebhookController extends CashierWebhookController
{
    public function __invoke(Request $request): Response
    {
        return $this->handleWebhook($request);
    }

    protected function handleProductCreated(array $payload): Response
    {
        $this->upsertProduct($payload);

        return $this->successMethod();
    }

    protected function handleProductUpdated(array $payload): Response
    {
        $this->upsertProduct($payload);

        return $this->successMethod();
    }

    protected function handleProductDeleted(array $payload): Response
    {
        $this->upsertProduct($payload);

        return $this->successMethod();
    }

    protected function handlePriceCreated(array $payload): Response
    {
        $this->upsertPrice($payload);

        return $this->successMethod();
    }

    protected function handlePriceUpdated(array $payload): Response
    {
        $this->upsertPrice($payload);

        return $this->successMethod();
    }

    protected function handlePriceDeleted(array $payload): Response
    {
        $this->upsertPrice($payload);

        return $this->successMethod();
    }

    protected function handleCustomerSubscriptionCreated(array $payload)
    {
        $response = parent::handleCustomerSubscriptionCreated($payload);
        $this->syncSubscriptionPlan($payload);

        return $response ?? $this->successMethod();
    }

    protected function handleCustomerSubscriptionUpdated(array $payload)
    {
        $response = parent::handleCustomerSubscriptionUpdated($payload);
        $this->syncSubscriptionPlan($payload);

        return $response ?? $this->successMethod();
    }

    protected function handleCustomerSubscriptionDeleted(array $payload)
    {
        $response = parent::handleCustomerSubscriptionDeleted($payload);
        $this->syncSubscriptionPlan($payload);

        return $response ?? $this->successMethod();
    }

    private function upsertProduct(array $payload): void
    {
        $object = data_get($payload, 'data.object');
        if (! $object) {
            return;
        }

        app(UpsertStripeProduct::class)->handle($object);
    }

    private function upsertPrice(array $payload): void
    {
        $object = data_get($payload, 'data.object');
        if (! $object) {
            return;
        }

        app(UpsertStripePrice::class)->handle($object);
    }

    private function syncSubscriptionPlan(array $payload): void
    {
        app(StripeSubscriptionPlanSyncService::class)->sync($payload);
    }
}
