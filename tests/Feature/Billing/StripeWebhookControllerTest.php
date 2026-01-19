<?php

use App\Models\BillingPrice;
use App\Models\BillingProduct;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Cashier\Subscription;

uses(RefreshDatabase::class);

it('stores subscription data and syncs plan tier from stripe webhook', function (): void {
    config()->set('cashier.webhook.secret', null);

    $user = User::factory()->create([
        'stripe_id' => 'cus_123',
        'plan_tier' => 'PRICE_STARTER',
    ]);

    $product = BillingProduct::query()->create([
        'key' => 'pixrup-pro-plan',
        'name' => 'Pixrup Pro',
        'type' => 'subscription',
        'is_active' => true,
    ]);

    BillingPrice::query()->create([
        'billing_product_id' => $product->id,
        'stripe_price_id' => 'price_pro',
        'stripe_product_id' => 'prod_pro',
        'type' => 'recurring',
        'currency' => 'USD',
        'unit_amount' => 500,
        'active' => true,
    ]);

    $payload = [
        'id' => 'evt_1',
        'type' => 'customer.subscription.created',
        'data' => [
            'object' => [
                'id' => 'sub_123',
                'customer' => 'cus_123',
                'status' => 'active',
                'items' => [
                    'data' => [
                        [
                            'id' => 'si_123',
                            'price' => [
                                'id' => 'price_pro',
                                'product' => 'prod_pro',
                            ],
                            'quantity' => 1,
                        ],
                    ],
                ],
            ],
        ],
    ];

    $this->postJson(route('stripe.webhook'), $payload)->assertOk();

    expect(Subscription::query()->where('stripe_id', 'sub_123')->exists())->toBeTrue();
    expect($user->refresh()->plan_tier)->toBe('PRICE_PRO');
});

it('resets plan tier when stripe webhook reports cancellation', function (): void {
    config()->set('cashier.webhook.secret', null);

    $user = User::factory()->create([
        'stripe_id' => 'cus_456',
        'plan_tier' => 'PRICE_PRO',
    ]);

    $payload = [
        'id' => 'evt_2',
        'type' => 'customer.subscription.deleted',
        'data' => [
            'object' => [
                'id' => 'sub_456',
                'customer' => 'cus_456',
                'status' => 'canceled',
            ],
        ],
    ];

    $this->postJson(route('stripe.webhook'), $payload)->assertOk();

    expect($user->refresh()->plan_tier)->toBe('PRICE_STARTER');
});
