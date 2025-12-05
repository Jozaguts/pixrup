<?php

use App\Application\Properties\DTOs\PropertyWorthDTO;
use App\Application\Properties\Services\AppraisalService;
use App\Application\Properties\UseCases\FetchPropertyWorthUseCase;
use App\Models\Property;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

/**
 * Test that the use case delegates valuation retrieval to the application service.
 * Expected Result: Service is invoked once and DTO is returned unchanged.
 */
test('fetch property worth use case returns dto from service', function (): void {
    $user = User::factory()->create();
    $property = Property::factory()->create(['user_id' => $user->id]);

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
