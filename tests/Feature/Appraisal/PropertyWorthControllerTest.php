<?php

use App\Domain\Appraisal\Providers\AppraisalProviderInterface;
use App\Infrastructure\Appraisal\Providers\MockAppraisalProvider;
use App\Models\Property;
use App\Models\User;
use App\Models\UsagePropertyMonthly;

/**
 * Test that the worth fetch endpoint returns valuation payload via Inertia.
 * Expected Result: Response status 200 with component props containing worth data keys.
 */
test('property worth endpoint returns valuation payload', function (): void {
    $property = Property::factory()->create();
    $user = User::factory()->create();
    $this->actingAs($user);

    app()->instance(AppraisalProviderInterface::class, new MockAppraisalProvider());

    $response = $this->withHeaders([
        'X-Inertia' => 'true',
        'X-Inertia-Version' => 'testing',
        'Accept' => 'application/json',
    ])->post(route('properties.worth.fetch', ['property' => $property->id]));

    $response->assertStatus(200)
        ->assertJsonStructure([
            'component',
            'props' => [
                'worth' => ['value', 'value_low', 'value_high', 'confidence', 'comparables', 'provider', 'fetched_at'],
            ],
        ]);
});

/**
 * Test that exceeding plan limits returns an HTTP 403 response.
 * Expected Result: Controller responds with Inertia error payload containing worth error message.
 */
test('property worth endpoint enforces plan limits', function (): void {
    config(['plans.tiers.professional.limit' => 0]);

    $property = Property::factory()->create();
    $user = User::factory()->create(['plan' => 'professional']);
    $this->actingAs($user);

    app()->instance(AppraisalProviderInterface::class, new MockAppraisalProvider());

    $response = $this->withHeaders([
        'X-Inertia' => 'true',
        'X-Inertia-Version' => 'testing',
        'Accept' => 'application/json',
    ])->post(route('properties.worth.fetch', ['property' => $property->id]));

    $response->assertStatus(403)
        ->assertJsonPath('props.errors.worth', 'You have reached your monthly property usage limit.');
});

/**
 * Test that cached valuations avoid duplicate provider calls for fresh data.
 * Expected Result: Second request hits cache, retains same values, and provider mock is invoked once.
 */
test('property worth endpoint reuses cached valuations', function (): void {
    $property = Property::factory()->create();
    $user = User::factory()->create();
    $this->actingAs($user);

    $provider = \Mockery::mock(AppraisalProviderInterface::class);
    $sampleProvider = new MockAppraisalProvider();
    $provider->shouldReceive('fetchValue')
        ->once()
        ->andReturn($sampleProvider->fetchValue($property));
    app()->instance(AppraisalProviderInterface::class, $provider);

    $headers = [
        'X-Inertia' => 'true',
        'X-Inertia-Version' => 'testing',
        'Accept' => 'application/json',
    ];

    $first = $this->withHeaders($headers)->post(route('properties.worth.fetch', ['property' => $property->id]));
    $first->assertStatus(200);

    $second = $this->withHeaders($headers)->post(route('properties.worth.fetch', ['property' => $property->id]));
    $second->assertStatus(200)
        ->assertJsonPath('props.worth.value', $first->json('props.worth.value'));

    expect(UsagePropertyMonthly::query()->count())->toBe(1);
});
