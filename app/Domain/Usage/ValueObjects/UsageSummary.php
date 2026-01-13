<?php

namespace App\Domain\Usage\ValueObjects;

use Carbon\CarbonInterface;

final class UsageSummary
{
    /**
     * @param array{limit:int, used:int, remaining:int|null} $docs
     * @param array{limit:int, used:int, remaining:int|null} $renders
     */
    public function __construct(
        public readonly string $tier,
        public readonly string $planLabel,
        public readonly string $periodKey,
        public readonly ?CarbonInterface $resetsAt,
        public readonly array $docs,
        public readonly array $renders,
    ) {
    }

    public function toArray(): array
    {
        $docs = $this->withUiFlags($this->docs);
        $renders = $this->withUiFlags($this->renders);

        return [
            'plan' => [
                'tier' => $this->tier,
                'label' => $this->planLabel,
            ],
            'usage' => [
                'docs' => $docs,
                'renders' => $renders,
            ],
            // Convenience flags at root (common UI checks)
            'can' => [
                'docs' => $docs['can_use'],
                'renders' => $renders['can_use'],
            ],
            'period_key' => $this->periodKey,
            'resets_at' => $this->resetsAt?->toIso8601String(),
        ];
    }

    /**
     * @param array{limit:int, used:int, remaining:int|null} $bucket
     * @return array{
     *   limit:int,
     *   used:int,
     *   remaining:int|null,
     *   is_unlimited:bool,
     *   is_blocked:bool,
     *   can_use:bool,
     *   percent_used:int|null
     * }
     */
    private function withUiFlags(array $bucket): array
    {
        $limit = (int) $bucket['limit'];
        $used = (int) $bucket['used'];

        $isUnlimited = $limit === -1;
        $isBlocked = $limit === 0;

        // Nota: "can_use" significa "puede usar en este instante":
        // - unlimited => true
        // - blocked => false
        // - quota => used < limit
        $canUse = $isUnlimited || ((!$isBlocked && $used < $limit));

        $percentUsed = null;
        if ($limit > 0) {
            $percentUsed = (int) min(100, floor(($used / $limit) * 100));
        }

        return [
            'limit' => $limit,
            'used' => $used,
            'remaining' => $bucket['remaining'], // null = unlimited
            'is_unlimited' => $isUnlimited,
            'is_blocked' => $isBlocked,
            'can_use' => $canUse,
            'percent_used' => $percentUsed,
        ];
    }
}
