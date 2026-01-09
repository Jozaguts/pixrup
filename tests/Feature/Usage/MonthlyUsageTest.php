<?php

use App\Application\Usage\Services\MonthlyPropertyUsageService;
use App\Application\Usage\Services\UsagePeriodService;
use App\Domain\Shared\Exceptions\FeatureLimitExceededException;
use App\Domain\Usage\Enums\UsageAction;
use App\Models\Property;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('usage resets before consumption when reset_at is in the past', function (): void {
    $period = app(UsagePeriodService::class)->current();
    $user = User::factory()->create([
        'plan_tier' => 'PRICE_PRO',
        'used_docs' => 7,
        'used_renders' => 3,
        'usage_reset_at' => CarbonImmutable::now('UTC')->subDay()->toDateTimeString(),
    ]);
    $property = Property::factory()->create(['user_id' => $user->id]);

    app(MonthlyPropertyUsageService::class)
        ->ensureUsage($user, $property->toEntity(), UsageAction::REPORT);

    $user->refresh();

    expect($user->used_docs)->toBe(1)
        ->and($user->used_renders)->toBe(0)
        ->and($user->usage_reset_at?->toDateTimeString())->toBe($period->resetsAt->toDateTimeString());
});

test('starter plan blocks docs after limit', function (): void {
    $period = app(UsagePeriodService::class)->current();
    $user = User::factory()->create([
        'plan_tier' => 'PRICE_STARTER',
        'used_docs' => 50,
        'usage_reset_at' => $period->resetsAt->toDateTimeString(),
    ]);
    $property = Property::factory()->create(['user_id' => $user->id]);
    $service = app(MonthlyPropertyUsageService::class);

    expect(fn () => $service->ensureUsage($user, $property->toEntity(), UsageAction::REPORT))
        ->toThrow(FeatureLimitExceededException::class);
});

test('starter plan blocks renders', function (): void {
    $period = app(UsagePeriodService::class)->current();
    $user = User::factory()->create([
        'plan_tier' => 'PRICE_STARTER',
        'used_renders' => 0,
        'usage_reset_at' => $period->resetsAt->toDateTimeString(),
    ]);
    $property = Property::factory()->create(['user_id' => $user->id]);
    $service = app(MonthlyPropertyUsageService::class);

    expect(fn () => $service->ensureUsage($user, $property->toEntity(), UsageAction::GLOW_UP))
        ->toThrow(FeatureLimitExceededException::class);
});

test('pro plan allows unlimited docs', function (): void {
    $period = app(UsagePeriodService::class)->current();
    $user = User::factory()->create([
        'plan_tier' => 'PRICE_PRO',
        'used_docs' => 999,
        'usage_reset_at' => $period->resetsAt->toDateTimeString(),
    ]);
    $property = Property::factory()->create(['user_id' => $user->id]);

    app(MonthlyPropertyUsageService::class)
        ->ensureUsage($user, $property->toEntity(), UsageAction::REPORT);

    $user->refresh();
    expect($user->used_docs)->toBe(1000);
});

test('pro plan blocks renders after limit', function (): void {
    $period = app(UsagePeriodService::class)->current();
    $user = User::factory()->create([
        'plan_tier' => 'PRICE_PRO',
        'used_renders' => 20,
        'usage_reset_at' => $period->resetsAt->toDateTimeString(),
    ]);
    $property = Property::factory()->create(['user_id' => $user->id]);
    $service = app(MonthlyPropertyUsageService::class);

    expect(fn () => $service->ensureUsage($user, $property->toEntity(), UsageAction::GLOW_UP))
        ->toThrow(FeatureLimitExceededException::class);
});

test('enterprise plan allows renders without limits', function (): void {
    $period = app(UsagePeriodService::class)->current();
    $user = User::factory()->create([
        'plan_tier' => 'PRICE_ENTERPRISE',
        'used_renders' => 250,
        'usage_reset_at' => $period->resetsAt->toDateTimeString(),
    ]);
    $property = Property::factory()->create(['user_id' => $user->id]);

    app(MonthlyPropertyUsageService::class)
        ->ensureUsage($user, $property->toEntity(), UsageAction::GLOW_UP);

    $user->refresh();
    expect($user->used_renders)->toBe(251);
});
