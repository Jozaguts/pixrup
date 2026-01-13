<?php

use App\Domain\Usage\Enums\UsageAction;
use App\Domain\Usage\ValueObjects\PlanInfo;
use Tests\TestCase;

uses(TestCase::class);

test('plan info enforces limits and bucket mapping', function (): void {
    $plan = new PlanInfo(
        tier: 'PRICE_STARTER',
        label: 'Starter',
        limits: ['docs' => 2, 'renders' => 0],
    );

    expect($plan->limitForBucket('docs'))->toBe(2)
        ->and($plan->limitForBucket('renders'))->toBe(0)
        ->and($plan->allows('docs', 1))->toBeTrue()
        ->and($plan->allows('docs', 2))->toBeFalse()
        ->and($plan->isBlocked('renders'))->toBeTrue()
        ->and($plan->allows('renders', 0))->toBeFalse()
        ->and($plan->bucketForAction(UsageAction::GLOW_UP))->toBe('renders')
        ->and($plan->bucketForAction(UsageAction::APPRAISAL))->toBe('docs');
});

test('plan info treats unlimited buckets as allowed', function (): void {
    $plan = new PlanInfo(
        tier: 'PRICE_ENTERPRISE',
        label: 'Enterprise',
        limits: ['docs' => -1, 'renders' => -1],
    );

    expect($plan->isUnlimited('docs'))->toBeTrue()
        ->and($plan->isUnlimited('renders'))->toBeTrue()
        ->and($plan->allows('docs', 999))->toBeTrue()
        ->and($plan->allows('renders', 999))->toBeTrue();
});
