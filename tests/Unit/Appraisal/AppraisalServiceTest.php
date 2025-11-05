<?php

use App\Application\Appraisal\DTOs\PropertyWorthDTO;
use App\Application\Appraisal\Services\AppraisalService;
use App\Application\Shared\Services\CheckFeatureLimit;
use App\Domain\Appraisal\Providers\AppraisalProviderInterface;
use App\Domain\Appraisal\Repositories\PropertyWorthRepositoryInterface;
use App\Infrastructure\Appraisal\Persistence\EloquentPropertyWorthRepository;
use App\Infrastructure\Appraisal\Providers\MockAppraisalProvider;
use App\Models\Property;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

/**
 * Test that a fresh valuation fetch persists data and respects plan limits.
 * Expected Result: Provider called once, repository stores record, plan limit hooks invoked.
 */
test('appraisal service stores valuation and updates plan usage', function (): void {
    $repository = app(PropertyWorthRepositoryInterface::class);
    expect($repository)->toBeInstanceOf(EloquentPropertyWorthRepository::class);

    $provider = \Mockery::mock(AppraisalProviderInterface::class);
    $featureLimit = \Mockery::mock(CheckFeatureLimit::class);

    $property = Property::factory()->create();
    $user = User::factory()->create();
    $this->actingAs($user);

    $dto = (new MockAppraisalProvider())->fetchValue($property);

    $provider->shouldReceive('fetchValue')
        ->once()
        ->with(\Mockery::on(fn ($arg) => $arg instanceof Property && $arg->is($property)))
        ->andReturn($dto);

    $featureLimit->shouldReceive('assertUsageAvailable')
        ->once()
        ->with($user, 'appraisal.fetch');
    $featureLimit->shouldReceive('recordUsage')
        ->once()
        ->with($user, 'appraisal.fetch');

    $service = new AppraisalService($repository, $provider, $featureLimit);

    $responseDto = $service->fetchValuation($property);

    expect($responseDto)
        ->toBeInstanceOf(PropertyWorthDTO::class)
        ->and($responseDto->value)->toEqual($dto->value)
        ->and($responseDto->provider)->toEqual($dto->provider);

    expect($property->worths()->count())->toBe(1);
});

/**
 * Test that cached valuations are reused within 24 hours.
 * Expected Result: Second request returns same data and no new provider call.
 */
test('appraisal service returns cached valuation when fresh', function (): void {
    $repository = app(PropertyWorthRepositoryInterface::class);

    $property = Property::factory()->create();
    $user = User::factory()->create();
    $this->actingAs($user);

    $featureLimit = \Mockery::mock(CheckFeatureLimit::class);
    $featureLimit->shouldReceive('assertUsageAvailable')
        ->once()
        ->with($user, 'appraisal.fetch');
    $featureLimit->shouldReceive('recordUsage')
        ->once()
        ->with($user, 'appraisal.fetch');

    $provider = \Mockery::mock(AppraisalProviderInterface::class);

    $dto = new PropertyWorthDTO(
        value: 500000,
        value_low: 480000,
        value_high: 520000,
        confidence: 0.92,
        comparables: [],
        provider: 'mock',
        fetched_at: Carbon::now(),
        cached_at: null,
    );

    $provider->shouldReceive('fetchValue')
        ->once()
        ->andReturn($dto);

    $service = new AppraisalService($repository, $provider, $featureLimit);

    $first = $service->fetchValuation($property);
    $second = $service->fetchValuation($property);

    expect($first->value)->toEqual($second->value)
        ->and($second->cached_at)->not()->toBeNull()
        ->and($second->provider)->toEqual('mock');
});
