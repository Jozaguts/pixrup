<?php

use App\Application\Appraisal\DTOs\PropertyWorthDTO;
use App\Application\Appraisal\Services\AppraisalService;
use App\Application\Appraisal\UseCases\FetchPropertyWorthUseCase;
use App\Models\Property;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

/**
 * Test that the use case delegates valuation retrieval to the application service.
 * Expected Result: Service is invoked once and DTO is returned unchanged.
 */
test('fetch property worth use case returns dto from service', function (): void {
    $property = Property::factory()->create();

    $dto = new PropertyWorthDTO(
        value: 410000,
        value_low: 395000,
        value_high: 425000,
        confidence: 0.81,
        comparables: [],
        provider: 'mock',
        fetched_at: Carbon::now(),
        cached_at: null,
    );

    $service = \Mockery::mock(AppraisalService::class);
    $service->shouldReceive('fetchValuation')
        ->once()
        ->with(\Mockery::on(fn ($arg) => $arg instanceof Property && $arg->is($property)))
        ->andReturn($dto);

    $useCase = new FetchPropertyWorthUseCase($service);

    $result = $useCase->execute($property);

    expect($result)->toBe($dto);
});
