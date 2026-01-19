<?php

use App\Application\Properties\PixrWorth\Contracts\WorthProvider;
use App\Application\Usage\Services\UsagePeriodService;
use App\Domain\Properties\Entities\PropertyEntity;
use App\Infrastructure\Property\PixrWorth\Providers\MockWorthProvider;
use App\Models\Property;
use App\Models\User;

/**
 * Test that the worth fetch endpoint returns valuation payload via JSON.
 * Expected Result: Response status 200 with JSON payload containing worth data keys.
 */
test('property worth endpoint returns valuation payload', function (): void {
    $user = User::factory()->create();
    createActiveSubscription($user);
    $property = Property::factory()->create(['user_id' => $user->id]);
    $this->actingAs($user);

    app()->instance(WorthProvider::class, new MockWorthProvider());

    $response = $this->post(route('properties.worth.fetch', ['property' => $property->id]));

    $response->assertStatus(200)
        ->assertJsonStructure([
            'worth' => ['value', 'value_low', 'value_high', 'confidence', 'comparables', 'provider', 'fetched_at'],
        ]);
});

/**
 * Test that exceeding plan limits returns an HTTP 403 response.
 * Expected Result: Controller responds with JSON error payload containing worth error message.
 */
test('property worth endpoint enforces plan limits', function (): void {
    $resetsAt = app(UsagePeriodService::class)->current()->resetsAt->toDateTimeString();
    $user = User::factory()->create([
        'plan_tier' => 'PRICE_STARTER',
        'used_docs' => 50,
        'usage_reset_at' => $resetsAt,
    ]);
    createActiveSubscription($user);
    $property = Property::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user);

    app()->instance(WorthProvider::class, new MockWorthProvider());

    $response = $this->post(route('properties.worth.fetch', ['property' => $property->id]));

    $response->assertStatus(403)
        ->assertJsonPath('message', 'You have reached your monthly property usage limit.');
});

/**
 * Test that cached valuations avoid duplicate provider calls for fresh data.
 * Expected Result: Second request hits cache, retains same values, and provider mock is invoked once.
 */
test('property worth endpoint reuses cached valuations', function (): void {
    $user = User::factory()->create();
    createActiveSubscription($user);
    $property = Property::factory()->create(['user_id' => $user->id]);
    $this->actingAs($user);

    $provider = \Mockery::mock(WorthProvider::class);

    $sampleProvider = new MockWorthProvider();
    $provider->shouldReceive('appraisal')
        ->once()
        ->with(\Mockery::on(fn ($arg) => $arg instanceof PropertyEntity && $arg->id === $property->id))
        ->andReturn($sampleProvider->appraisal($property->toEntity()));
    app()->instance(WorthProvider::class, $provider);

    $first = $this->post(route('properties.worth.fetch', ['property' => $property->id]));
    $first->assertStatus(200);

    $second = $this->post(route('properties.worth.fetch', ['property' => $property->id]));
    $second->assertStatus(200)
        ->assertJsonPath('worth.value', $first->json('worth.value'));

    $user->refresh();
    expect($user->used_docs)->toBe(1);
});
