<?php

namespace App\Domain\PixHunt\Runes;

use App\Domain\PixHunt\Concerns\HasSpyHuntCache;
use App\Domain\PixHunt\Concerns\HandlesRuneDefaults;
use App\Domain\PixHunt\Concerns\ComputesRuneConfidence;

class MarketVelocityRune
{
    use HandlesRuneDefaults;
    use ComputesRuneConfidence;
    use HasSpyHuntCache;

    protected function runeName(): string
    {
        return 'market_velocity';
    }

    /**
     * Public PixVision payload
     */
    public function runePayload(): array
    {
        return [
            'rune_value' => [
                'median_days_on_market' => $this->medianDaysOnMarket(),
                'velocity'              => $this->velocityClass(),
                'sample_size'           => $this->comparablesCount(),
                'summary'               => $this->summary(),
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
            'has_comps'    => $this->hasComparables(),
            'sample_size'  => $this->comparablesCount(),
            'dom_variance' => $this->domVariance(),
        ];
    }

    /**
     * Confidence weighting rules
     */
    protected function confidenceBoost(string $signal, mixed $value): float
    {
        return match ($signal) {
            'has_comps'    => $value ? 0.30 : -0.60,
            'sample_size' => $this->normalizeSampleBoost($value),
            'dom_variance'=> $this->variancePenalty($value),
            default        => 0.0,
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
     * Core calculations
     */
    protected function medianDaysOnMarket(): ?int
    {
        $doms = $this->daysOnMarketValues();

        if (empty($doms)) {
            return null;
        }

        sort($doms);

        $count  = count($doms);
        $middle = intdiv($count, 2);

        return $count % 2
            ? $doms[$middle]
            : (int) round(($doms[$middle - 1] + $doms[$middle]) / 2);
    }

    protected function velocityClass(): string
    {
        $median = $this->medianDaysOnMarket();

        if ($median === null) {
            return 'unknown';
        }

        return match (true) {
            $median <= 30 => 'fast',
            $median <= 75 => 'normal',
            default       => 'slow',
        };
    }

    /**
     * Helpers
     */
    protected function saleComparables(): array
    {
        return $this->cache['payload']['comps']['sale'] ?? [];
    }

    protected function daysOnMarketValues(): array
    {
        return array_values(
            array_filter(
                array_map(
                    static fn ($comp) => $comp['daysOnMarket'] ?? null,
                    $this->saleComparables()
                ),
                static fn ($v) => is_numeric($v) && $v > 0
            )
        );
    }

    protected function domVariance(): ?float
    {
        $values = $this->daysOnMarketValues();

        if (count($values) < 3) {
            return null;
        }

        $mean = array_sum($values) / count($values);

        $variance = array_sum(
                array_map(
                    static fn ($v) => ($v - $mean) ** 2,
                    $values
                )
            ) / count($values);

        return round(sqrt($variance), 2);
    }

    /**
     * Confidence helpers
     */
    protected function normalizeSampleBoost(int $count): float
    {
        return match (true) {
            $count >= 15 => 0.25,
            $count >= 8  => 0.15,
            $count >= 4  => 0.05,
            default      => -0.20,
        };
    }

    protected function variancePenalty(?float $variance): float
    {
        if ($variance === null) {
            return -0.20;
        }

        return match (true) {
            $variance <= 15 => 0.15,
            $variance <= 30 => 0.05,
            default         => -0.15,
        };
    }

    protected function summary(): string
    {
        $median = $this->medianDaysOnMarket();
        $count  = $this->comparablesCount();
        $class  = $this->velocityClass();

        if ($median === null || $count === 0) {
            return 'Insufficient sales data to determine market velocity.';
        }

        return match ($class) {
            'fast' =>
            "Fast-moving market with a median of {$median} days on market across {$count} comparable sales.",

            'normal' =>
            "Normal market pace with homes selling in a median of {$median} days based on {$count} comparables.",

            'slow' =>
            "Slow market conditions with a median of {$median} days on market from {$count} comparable sales.",

            default =>
            'Market velocity could not be determined from available data.',
        };
    }
}
