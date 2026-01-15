<?php

use App\Application\Billing\Services\BillingPlanService;
use App\Models\BillingPrice;
use App\Models\BillingProduct;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Cashier\Subscription;

uses(RefreshDatabase::class);

it('marks active plans as canceling when within grace period', function (): void {
    $user = User::factory()->create([
        'stripe_id' => 'cus_canceling',
    ]);

    $product = BillingProduct::query()->create([
        'key' => 'pixrup-starter-plan',
        'name' => 'Pixrup Starter',
        'type' => 'subscription',
        'is_active' => true,
    ]);

    BillingPrice::query()->create([
        'billing_product_id' => $product->id,
        'stripe_price_id' => 'price_starter',
        'stripe_product_id' => 'prod_starter',
        'type' => 'recurring',
        'currency' => 'USD',
        'unit_amount' => 500,
        'active' => true,
    ]);

    $endsAt = now()->addDays(10);

    Subscription::query()->create([
        'user_id' => $user->id,
        'type' => 'default',
        'stripe_id' => 'sub_123',
        'stripe_status' => 'active',
        'stripe_price' => 'price_starter',
        'quantity' => 1,
        'trial_ends_at' => null,
        'ends_at' => $endsAt,
    ]);

    $service = app(BillingPlanService::class);
    $catalog = $service->catalog($user);

    expect($catalog['active_plan'])->not->toBeNull();
    expect($catalog['active_plan']['is_canceling'])->toBeTrue();
    expect($catalog['active_plan']['ends_at'])->toBe($endsAt->toDateString());
});
