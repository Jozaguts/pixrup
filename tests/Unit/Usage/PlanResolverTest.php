<?php

use App\Application\Usage\Services\PlanResolver;
use App\Models\User;
use Tests\TestCase;

uses(TestCase::class);

test('plan resolver reads plan_tier limits', function (): void {
    $user = User::factory()->make(['plan_tier' => 'PRICE_STARTER']);

    $plan = app(PlanResolver::class)->resolve($user);

    expect($plan->tier)->toBe('PRICE_STARTER')
        ->and($plan->label)->toBe('Starter')
        ->and($plan->limits)->toEqual(['docs' => 50, 'renders' => 0]);
});

test('plan resolver falls back to default when tier is invalid', function (): void {
    $user = User::factory()->make(['plan_tier' => 'PRICE_UNKNOWN']);

    $plan = app(PlanResolver::class)->resolve($user);

    expect($plan->tier)->toBe(config('plans.default'));
});

test('plan resolver accepts aliases', function (): void {
    $user = User::factory()->make(['plan_tier' => 'pro']);

    $plan = app(PlanResolver::class)->resolve($user);

    expect($plan->tier)->toBe('PRICE_PRO')
        ->and($plan->limits)->toEqual(['docs' => -1, 'renders' => 20]);
});
