<?php

declare(strict_types=1);

namespace App\Interface\Properties\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Application\Billing\Services\BillingPlanService;
use App\Interface\Properties\Http\Requests\CancelSubscriptionRequest;
use App\Interface\Properties\Http\Requests\SubscribePlanRequest;
use App\Interface\Properties\Http\Requests\SwapPlanRequest;
use App\Models\BillingPrice;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BillingController extends Controller
{
    private const ORDER_HISTORY_PER_PAGE = 5;
    private const ORDER_HISTORY_MAX_PER_PAGE = 25;

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
        $orderHistory = [
            'data' => [],
            'next_cursor' => null,
        ];

        if ($user) {
            if (!$user->stripe_id) {
                $user->createAsStripeCustomer();
            }

            $paymentMethods = $user->paymentMethods();
            $defaultPaymentMethodId = $user->defaultPaymentMethod()?->id;
            $setupIntent = $user->createSetupIntent();
            $planCatalog = $planService->catalog($user);
            $orderHistory = $this->buildOrderHistoryPayload($user);
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
            'orderHistory' => $orderHistory,
        ]);
    }

    public function orderHistory(Request $request): JsonResponse
    {
        $user = $request->user();
        if (! $user) {
            abort(401);
        }

        return response()->json(
            $this->buildOrderHistoryPayload(
                $user,
                $request->query('cursor'),
                $this->resolveOrderHistoryPerPage($request),
            )
        );
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

    private function buildOrderHistoryPayload(User $user, ?string $cursor = null, ?int $perPage = null): array
    {
        $resolvedPerPage = $perPage ?? self::ORDER_HISTORY_PER_PAGE;

        $paginator = $user->cursorPaginateInvoices(
            $resolvedPerPage,
            ['status' => 'paid'],
            'cursor',
            $cursor,
        );

        $data = collect($paginator->items())
            ->map(fn ($invoice) => [
                'id' => $invoice->id,
                'date' => $invoice->date()->toFormattedDateString(),
                'type' => $this->resolveInvoicePlanName($invoice),
                'receipt_url' => $invoice->hosted_invoice_url ?? $invoice->invoice_pdf,
            ])
            ->values();

        return [
            'data' => $data,
            'next_cursor' => $paginator->nextCursor()?->encode(),
            'has_more' => $paginator->hasMorePages(),
        ];
    }

    private function resolveInvoicePlanName($invoice): string
    {
        $planName = (string) ($invoice->description ?? '');

        try {
            $subscriptionLines = $invoice->subscriptions();
            $line = $subscriptionLines[0] ?? null;
            if ($line) {
                $lineDescription = (string) ($line->description ?? '');
                if ($lineDescription !== '') {
                    $planName = $lineDescription;
                }

                $priceId = (string) data_get($line, 'price.id', '');
                if ($planName === '' && $priceId !== '') {
                    $price = BillingPrice::query()
                        ->with('product')
                        ->where('stripe_price_id', $priceId)
                        ->first();

                    $planName = (string) ($price?->product?->name ?? '');
                }
            }
        } catch (\Throwable $exception) {
            // Fallbacks below handle any Stripe API issues.
        }

        return $planName !== '' ? $planName : 'Subscription';
    }

    private function resolveOrderHistoryPerPage(Request $request): int
    {
        $perPage = (int) $request->query('per_page', self::ORDER_HISTORY_PER_PAGE);

        if ($perPage < 1) {
            return self::ORDER_HISTORY_PER_PAGE;
        }

        return min($perPage, self::ORDER_HISTORY_MAX_PER_PAGE);
    }
}
