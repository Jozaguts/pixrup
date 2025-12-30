<?php

use App\Infrastructure\Property\PixrWorth\Providers\MockWorthProvider;
use App\Models\Property;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

/**
 * Test that the mock worth provider returns deterministic valuation data.
 * Expected Result: DTO contains provider "mock", numeric valuation, and comparable entries.
 */
test('mock provider returns deterministic valuation payload', function (): void {
    $user = User::factory()->create();
    $property = Property::factory()->create(['user_id' => $user->id]);

    $provider = new MockWorthProvider();
    $dto = $provider->appraisal($property->toEntity());

    expect($dto->provider)->toBe('mock')
        ->and($dto->value)->toBeFloat()
        ->and($dto->comparables)->toBeArray()
        ->and($dto->comparables)->toHaveCount(2)
        ->and($dto->fetched_at->isToday())->toBeTrue();
});
