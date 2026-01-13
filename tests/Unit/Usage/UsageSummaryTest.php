<?php

use App\Domain\Usage\ValueObjects\UsageSummary;
use Carbon\CarbonImmutable;
use Tests\TestCase;

uses(TestCase::class);

test('usage summary flags blocked buckets correctly', function (): void {
    $summary = new UsageSummary(
        tier: 'PRICE_STARTER',
        planLabel: 'Starter',
        periodKey: '2024-01',
        resetsAt: CarbonImmutable::parse('2024-02-01T00:00:00Z'),
        docs: ['limit' => 50, 'used' => 10, 'remaining' => 40],
        renders: ['limit' => 0, 'used' => 0, 'remaining' => 0],
    );

    $payload = $summary->toArray();

    expect($payload['usage']['renders']['is_blocked'])->toBeTrue()
        ->and($payload['usage']['renders']['can_use'])->toBeFalse()
        ->and($payload['usage']['renders']['remaining'])->toBe(0)
        ->and($payload['usage']['renders']['percent_used'])->toBeNull();
});

test('usage summary flags unlimited buckets correctly', function (): void {
    $summary = new UsageSummary(
        tier: 'PRICE_PRO',
        planLabel: 'Pro',
        periodKey: '2024-01',
        resetsAt: CarbonImmutable::parse('2024-02-01T00:00:00Z'),
        docs: ['limit' => -1, 'used' => 5, 'remaining' => null],
        renders: ['limit' => 20, 'used' => 10, 'remaining' => 10],
    );

    $payload = $summary->toArray();

    expect($payload['usage']['docs']['is_unlimited'])->toBeTrue()
        ->and($payload['usage']['docs']['remaining'])->toBeNull()
        ->and($payload['usage']['docs']['percent_used'])->toBeNull()
        ->and($payload['usage']['renders']['percent_used'])->toBe(50);
});
