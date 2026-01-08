<?php

namespace App\Http\Controllers\Billing;

use App\Http\Controllers\Controller;
use App\Infrastructure\Billing\Handlers\UpsertStripePrice;
use App\Infrastructure\Billing\Handlers\UpsertStripeProduct;
use Illuminate\Http\Request;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;
use Symfony\Component\HttpFoundation\Response;

class StripeWebhookController extends Controller
{
    public function __invoke(Request $request, UpsertStripeProduct $upsertProduct, UpsertStripePrice $upsertPrice): Response
    {
        $payload = $request->getContent();
        $signature = $request->header('Stripe-Signature', '');
        $secret = config('cashier.webhook.secret');

        if (!$secret) {
            return response('Stripe webhook secret not configured.', 500);
        }

        try {
            $event = Webhook::constructEvent(
                $payload,
                $signature,
                $secret,
                (int) config('cashier.webhook.tolerance', 300)
            );
        } catch (SignatureVerificationException) {
            return response('Invalid signature.', 400);
        } catch (\UnexpectedValueException) {
            return response('Invalid payload.', 400);
        }

        $object = $event->data->object ?? null;
        if (!$object) {
            return response('Missing payload.', 400);
        }

        switch ($event->type) {
            case 'product.created':
            case 'product.updated':
            case 'product.deleted':
                $upsertProduct->handle($object);
                break;
            case 'price.created':
            case 'price.updated':
            case 'price.deleted':
                $upsertPrice->handle($object);
                break;
            default:
                break;
        }

        return response('ok', 200);
    }
}
