<?php

use App\Application\Billing\Services\BillingPlanService;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

afterEach(function (): void {
    \Mockery::close();
});

it('subscribes to a plan through the billing controller', function (): void {
    $user = User::factory()->create();

    $service = \Mockery::mock(BillingPlanService::class);
    $service->shouldReceive('subscribe')
        ->once()
        ->withArgs(fn (User $arg, string $priceId) => $arg->is($user) && $priceId === 'price_123');

    app()->instance(BillingPlanService::class, $service);

    $this->actingAs($user)
        ->post(route('billing.subscription.store'), ['price_id' => 'price_123'])
        ->assertStatus(303);
});

it('swaps a plan through the billing controller', function (): void {
    $user = User::factory()->create();

    $service = \Mockery::mock(BillingPlanService::class);
    $service->shouldReceive('swap')
        ->once()
        ->withArgs(fn (User $arg, string $priceId) => $arg->is($user) && $priceId === 'price_456');

    app()->instance(BillingPlanService::class, $service);

    $this->actingAs($user)
        ->post(route('billing.subscription.swap'), ['price_id' => 'price_456'])
        ->assertStatus(303);
});

it('cancels a subscription at period end through the billing controller', function (): void {
    $user = User::factory()->create();

    $service = \Mockery::mock(BillingPlanService::class);
    $service->shouldReceive('cancel')
        ->once()
        ->withArgs(fn (User $arg) => $arg->is($user));

    app()->instance(BillingPlanService::class, $service);

    $this->actingAs($user)
        ->post(route('billing.subscription.cancel'))
        ->assertStatus(303);
});
