<?php

use App\Application\Billing\Services\StripeSubscriptionPlanSyncService;
use App\Models\BillingPrice;
use App\Models\BillingProduct;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('syncs plan tier for active subscriptions using product key aliases', function (): void {
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
        'data' => [
            'object' => [
                'customer' => 'cus_123',
                'status' => 'active',
                'items' => [
                    'data' => [
                        ['price' => ['id' => 'price_pro']],
                    ],
                ],
            ],
        ],
    ];

    app(StripeSubscriptionPlanSyncService::class)->sync($payload);

    expect($user->refresh()->plan_tier)->toBe('PRICE_PRO');
});

it('resets plan tier when subscription is canceled', function (): void {
    $user = User::factory()->create([
        'stripe_id' => 'cus_456',
        'plan_tier' => 'PRICE_PRO',
    ]);

    $payload = [
        'data' => [
            'object' => [
                'customer' => 'cus_456',
                'status' => 'canceled',
                'items' => ['data' => []],
            ],
        ],
    ];

    app(StripeSubscriptionPlanSyncService::class)->sync($payload);

    expect($user->refresh()->plan_tier)->toBe('PRICE_STARTER');
});

it('ignores micro-use products even when marked as subscription', function (): void {
    $user = User::factory()->create([
        'stripe_id' => 'cus_micro',
        'plan_tier' => 'PRICE_STARTER',
    ]);

    $product = BillingProduct::query()->create([
        'key' => 'pixrup-micro-use',
        'name' => 'Pixrup Micro Use',
        'type' => 'subscription',
        'is_active' => true,
    ]);

    BillingPrice::query()->create([
        'billing_product_id' => $product->id,
        'stripe_price_id' => 'price_micro',
        'stripe_product_id' => 'prod_micro',
        'type' => 'recurring',
        'currency' => 'USD',
        'unit_amount' => 500,
        'active' => true,
    ]);

    $payload = [
        'data' => [
            'object' => [
                'customer' => 'cus_micro',
                'status' => 'active',
                'items' => [
                    'data' => [
                        ['price' => ['id' => 'price_micro']],
                    ],
                ],
            ],
        ],
    ];

    app(StripeSubscriptionPlanSyncService::class)->sync($payload);

    expect($user->refresh()->plan_tier)->toBe('PRICE_STARTER');
});

it('ignores non-subscription products for tier sync', function (): void {
    $user = User::factory()->create([
        'stripe_id' => 'cus_one_time',
        'plan_tier' => 'PRICE_STARTER',
    ]);

    $product = BillingProduct::query()->create([
        'key' => 'pixrup-starter-plan',
        'name' => 'Pixrup Starter',
        'type' => 'one_time',
        'is_active' => true,
    ]);

    BillingPrice::query()->create([
        'billing_product_id' => $product->id,
        'stripe_price_id' => 'price_one_time',
        'stripe_product_id' => 'prod_one_time',
        'type' => 'one_time',
        'currency' => 'USD',
        'unit_amount' => 500,
        'active' => true,
    ]);

    $payload = [
        'data' => [
            'object' => [
                'customer' => 'cus_one_time',
                'status' => 'active',
                'items' => [
                    'data' => [
                        ['price' => ['id' => 'price_one_time']],
                    ],
                ],
            ],
        ],
    ];

    app(StripeSubscriptionPlanSyncService::class)->sync($payload);

    expect($user->refresh()->plan_tier)->toBe('PRICE_STARTER');
});
