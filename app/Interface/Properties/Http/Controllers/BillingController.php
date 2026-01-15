<?php

declare(strict_types=1);

namespace App\Interface\Properties\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Application\Billing\Services\BillingPlanService;
use App\Interface\Properties\Http\Requests\CancelSubscriptionRequest;
use App\Interface\Properties\Http\Requests\SubscribePlanRequest;
use App\Interface\Properties\Http\Requests\SwapPlanRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BillingController extends Controller
{
    public function account(Request $request, BillingPlanService $planService): Response
    {
        $user = $request->user();
        $paymentMethods = collect();
        $defaultPaymentMethodId = null;
        $setupIntent = null;
        $planCatalog = [
            'plans' => [],
            'active_plan' => null,
        ];

        if ($user) {
            if (!$user->stripe_id) {
                $user->createAsStripeCustomer();
            }

            $paymentMethods = $user->paymentMethods();
            $defaultPaymentMethodId = $user->defaultPaymentMethod()?->id;
            $setupIntent = $user->createSetupIntent();
            $planCatalog = $planService->catalog($user);
            $orderHistory = $user->invoices()
                ->map(fn ($invoice) => [
                    'date' => $invoice->date()->toFormattedDateString(),
                    'type' => $invoice->description ?? 'Subscription',
                    'receipt_url' => $invoice->hosted_invoice_url ?? $invoice->invoice_pdf,
                ])
                ->values();
        }

        return Inertia::render('billing/Account', [
            'stripeKey' => config('cashier.key'),
            'paymentMethods' => $paymentMethods->map(fn ($method) => [
                'id' => $method->id,
                'brand' => $method->card?->brand,
                'last4' => $method->card?->last4,
                'exp_month' => $method->card?->exp_month,
                'exp_year' => $method->card?->exp_year,
            ])->values(),
            'defaultPaymentMethodId' => $defaultPaymentMethodId,
            'setupIntent' => $setupIntent ? [
                'client_secret' => $setupIntent->client_secret,
            ] : null,
            'plans' => $planCatalog['plans'],
            'activePlan' => $planCatalog['active_plan'],
            'orderHistory' => $orderHistory ?? [],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'payment_method' => ['required', 'string'],
        ]);

        $user = $request->user();
        if (!$user) {
            abort(401);
        }

        if (!$user->stripe_id) {
            $user->createAsStripeCustomer();
        }

        $user->addPaymentMethod($data['payment_method']);
        $user->updateDefaultPaymentMethod($data['payment_method']);

        return back(303);
    }

    public function updateDefault(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'payment_method' => ['required', 'string'],
        ]);

        $user = $request->user();
        if (!$user) {
            abort(401);
        }

        $paymentMethod = $user->findPaymentMethod($data['payment_method']);
        if (!$paymentMethod) {
            abort(404);
        }

        $user->updateDefaultPaymentMethod($paymentMethod->id);

        return back(303);
    }

    public function destroy(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'payment_method' => ['nullable', 'string'],
        ]);

        $user = $request->user();
        if (!$user) {
            abort(401);
        }

        $paymentMethodId = $data['payment_method'] ?? null;
        $paymentMethod = $paymentMethodId
            ? $user->findPaymentMethod($paymentMethodId)
            : $user->defaultPaymentMethod();

        if (!$paymentMethod) {
            return back(303);
        }

        $paymentMethod->delete();

        $remainingMethods = $user->paymentMethods();
        if ($remainingMethods->isNotEmpty()) {
            $user->updateDefaultPaymentMethod($remainingMethods->first()->id);
        }

        return back(303);
    }

    public function subscribe(
        SubscribePlanRequest $request,
        BillingPlanService $planService,
    ): RedirectResponse {
        $user = $request->user();
        if (! $user) {
            abort(401);
        }

        $payload = $request->validated();
        $planService->subscribe($user, $payload['price_id']);

        return back(303);
    }

    public function swap(
        SwapPlanRequest $request,
        BillingPlanService $planService,
    ): RedirectResponse {
        $user = $request->user();
        if (! $user) {
            abort(401);
        }

        $payload = $request->validated();
        $planService->swap($user, $payload['price_id']);

        return back(303);
    }

    public function cancel(
        CancelSubscriptionRequest $request,
        BillingPlanService $planService,
    ): RedirectResponse {
        $user = $request->user();
        if (! $user) {
            abort(401);
        }

        $planService->cancel($user);

        return back(303);
    }
}
