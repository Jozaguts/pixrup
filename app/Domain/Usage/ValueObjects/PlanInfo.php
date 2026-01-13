<?php

namespace App\Domain\Usage\ValueObjects;

use App\Domain\Usage\Enums\UsageAction;

final readonly class PlanInfo
{
    /**
     * @param array{docs:int, renders:int} $limits
     */
    public function __construct(
        public string $tier,   // PRICE_STARTER | PRICE_PRO | PRICE_ENTERPRISE
        public string $label,  // Starter | Pro | Enterprise
        public array $limits,  // ['docs' => 50|-1|0, 'renders' => 20|-1|0]
    ) {}

    /**
     * -1 = unlimited
     *  0 = blocked
     * >0 = monthly quota
     */
    public function limitForBucket(string $bucket): int
    {
        return $this->limits[$bucket] ?? -1;
    }

    public function isUnlimited(string $bucket): bool
    {
        return $this->limitForBucket($bucket) === -1;
    }

    public function isBlocked(string $bucket): bool
    {
        return $this->limitForBucket($bucket) === 0;
    }

    public function allows(string $bucket, int $used): bool
    {
        $limit = $this->limitForBucket($bucket);

        if ($limit === -1) {
            return true;
        }

        if ($limit === 0) {
            return false;
        }

        return $used < $limit;
    }

    /**
     * Conveniencia: resolver bucket desde UsageAction
     */
    public function bucketForAction(UsageAction $action): string
    {
        return match ($action) {
            UsageAction::GLOW_UP => 'renders',
            UsageAction::APPRAISAL,
            UsageAction::SPY_HUNT,
            UsageAction::REPORT => 'docs',
        };
    }

    public function limitForAction(UsageAction $action): int
    {
        return $this->limitForBucket(
            $this->bucketForAction($action)
        );
    }
}
