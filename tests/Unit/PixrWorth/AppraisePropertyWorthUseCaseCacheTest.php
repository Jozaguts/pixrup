<?php

use App\Application\Properties\DTOs\PropertyWorthDTO;
use App\Application\Properties\PixrWorth\Contracts\WorthProvider;
use App\Application\Properties\PixrWorth\Contracts\WorthRepository;
use App\Application\Properties\PixrWorth\UseCases\AppraisePropertyWorthUseCase;
use App\Application\Usage\Contracts\UsageGuard;
use App\Models\Property;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

/**
 * Test that cached valuations are reused within 24 hours.
 * Expected Result: Cached DTO returned and no provider or usage calls occur.
 */
test('appraise property worth use case returns cached valuation when fresh', function (): void {
    $user = User::factory()->create();
    $property = Property::factory()->create(['user_id' => $user->id]);
    $entity = $property->toEntity();

    $dto = new PropertyWorthDTO(
        value: 410000,
        value_low: 395000,
        value_high: 425000,
        confidence: 0.81,
        comparables: [],
        provider: 'mock',
        fetched_at: Carbon::now(),
        cached_at: Carbon::now(),
    );

    $repo = \Mockery::mock(WorthRepository::class);
    $provider = \Mockery::mock(WorthProvider::class);
    $usage = \Mockery::mock(UsageGuard::class);

    $repo->shouldReceive('findFresh')
        ->once()
        ->with($property->id, \Mockery::type(\DateTimeInterface::class))
        ->andReturn($dto);

    $usage->shouldNotReceive('ensure');
    $provider->shouldNotReceive('appraisal');
    $repo->shouldNotReceive('save');

    $useCase = new AppraisePropertyWorthUseCase($repo, $provider, $usage);

    $result = $useCase->execute($entity);

    expect($result)->toBe($dto);
});
