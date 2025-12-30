<?php

use App\Application\Properties\DTOs\PropertyWorthDTO;
use App\Application\Properties\PixrWorth\Contracts\WorthProvider;
use App\Application\Properties\PixrWorth\Contracts\WorthRepository;
use App\Application\Properties\PixrWorth\UseCases\AppraisePropertyWorthUseCase;
use App\Application\Usage\Contracts\UsageGuard;
use App\Domain\Usage\Enums\UsageAction;
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
test('appraise property worth use case stores valuation and updates plan usage', function (): void {
    $user = User::factory()->create();
    $property = Property::factory()->create(['user_id' => $user->id]);
    $entity = $property->toEntity();

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

    $repo = \Mockery::mock(WorthRepository::class);
    $provider = \Mockery::mock(WorthProvider::class);
    $usage = \Mockery::mock(UsageGuard::class);

    $repo->shouldReceive('findFresh')
        ->once()
        ->with($property->id, \Mockery::type(\DateTimeInterface::class))
        ->andReturn(null);

    $usage->shouldReceive('ensure')
        ->once()
        ->with(UsageAction::APPRAISAL, $entity);

    $provider->shouldReceive('appraisal')
        ->once()
        ->with($entity)
        ->andReturn($dto);

    $repo->shouldReceive('save')
        ->once()
        ->with($property->id, $dto);

    $useCase = new AppraisePropertyWorthUseCase($repo, $provider, $usage);

    $responseDto = $useCase->execute($entity);

    expect($responseDto)
        ->toBeInstanceOf(PropertyWorthDTO::class)
        ->and($responseDto->value)->toEqual($dto->value)
        ->and($responseDto->provider)->toEqual($dto->provider);
});
