<?php

use App\Infrastructure\Appraisal\Providers\MockAppraisalProvider;
use App\Models\Property;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

/**
 * Test that the mock appraisal provider returns deterministic valuation data.
 * Expected Result: DTO contains provider "mock", numeric valuation, and comparable entries.
 */
test('mock provider returns deterministic valuation payload', function (): void {
    $property = Property::factory()->create();

    $provider = new MockAppraisalProvider();
    $dto = $provider->fetchValue($property);

    expect($dto->provider)->toBe('mock')
        ->and($dto->value)->toBeFloat()
        ->and($dto->comparables)->toBeArray()
        ->and($dto->comparables)->toHaveCount(2)
        ->and($dto->fetched_at->isToday())->toBeTrue();
});
