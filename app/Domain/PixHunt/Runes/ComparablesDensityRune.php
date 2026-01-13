<?php

namespace App\Domain\PixHunt\Runes;

use App\Domain\PixHunt\Concerns\HasSpyHuntCache;
use App\Domain\PixHunt\Concerns\ComputesRuneConfidence;
use App\Domain\PixHunt\Concerns\HandlesRuneDefaults;

class ComparablesDensityRune
{
    use HandlesRuneDefaults;
    use ComputesRuneConfidence;
    use HasSpyHuntCache;

    protected function runeName(): string
    {
        return 'comps_density';
    }

    /**
     * Public PixVision payload
     */
    public function runePayload(): array
    {
        return [
            'rune_value' => [
                'count'         => $this->comparablesCount(),
                'classification'=> $this->densityClass(),
            ],
            'confidence' => $this->computeConfidence(
                $this->confidenceSignals()
            ),
        ];
    }

    /**
     * Confidence signals
     */
    protected function confidenceSignals(): array
    {
        return [
            'has_comps'   => $this->hasComparables(),
            'comps_count' => $this->comparablesCount(),
        ];
    }

    /**
     * Confidence weighting
     */
    protected function confidenceBoost(string $signal, mixed $value): float
    {
        return match ($signal) {
            'has_comps'   => $value ? 0.35 : -0.70,
            'comps_count'=> $this->normalizeDensityBoost($value),
            default       => 0.0,
        };
    }

    /**
     * Guards
     */
    protected function hasComparables(): bool
    {
        return $this->comparablesCount() > 0;
    }

    protected function comparablesCount(): int
    {
        return count($this->saleComparables());
    }

    /**
     * Core classification
     */
    protected function densityClass(): string
    {
        $count = $this->comparablesCount();

        return match (true) {
            $count >= 15 => 'very_dense',
            $count >= 8  => 'dense',
            $count >= 4  => 'light',
            $count >= 1  => 'thin',
            default      => 'none',
        };
    }

    /**
     * Helpers
     */
    protected function saleComparables(): array
    {
        return $this->cache['payload']['comps']['sale'] ?? [];
    }

    protected function normalizeDensityBoost(int $count): float
    {
        return match (true) {
            $count >= 15 => 0.30,
            $count >= 8  => 0.20,
            $count >= 4  => 0.10,
            $count >= 1  => -0.10,
            default      => -0.40,
        };
    }
}
