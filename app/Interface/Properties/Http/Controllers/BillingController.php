<?php

namespace App\Interface\Properties\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BillingController extends Controller
{
    public function account(Request $request): Response
    {
        $user = $request->user();
        $paymentMethod = null;
        $setupIntent = null;

        if ($user) {
            if (!$user->stripe_id) {
                $user->createAsStripeCustomer();
            }

            if ($user->hasDefaultPaymentMethod()) {
                $paymentMethod = $user->defaultPaymentMethod();
            } else {
                $setupIntent = $user->createSetupIntent();
            }
        }

        return Inertia::render('billing/Account', [
            'stripeKey' => config('cashier.key'),
            'paymentMethod' => $paymentMethod ? [
                'id' => $paymentMethod->id,
                'brand' => $paymentMethod->card?->brand,
                'last4' => $paymentMethod->card?->last4,
                'exp_month' => $paymentMethod->card?->exp_month,
                'exp_year' => $paymentMethod->card?->exp_year,
            ] : null,
            'setupIntent' => $setupIntent ? [
                'client_secret' => $setupIntent->client_secret,
            ] : null,
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
}
