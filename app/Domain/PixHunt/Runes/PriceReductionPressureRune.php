<?php

namespace App\Domain\PixHunt\Runes;

use App\Domain\PixHunt\Concerns\HasSpyHuntCache;
use App\Domain\PixHunt\Concerns\HandlesRuneDefaults;
use App\Domain\PixHunt\Concerns\ComputesRuneConfidence;

class PriceReductionPressureRune
{
    use HandlesRuneDefaults;
    use ComputesRuneConfidence;
    use HasSpyHuntCache;

    protected function runeName(): string
    {
        return 'price_reduction_pressure';
    }

    /**
     * Public PixVision payload
     */
    public function runePayload(): array
    {
        return [
            'rune_value' => [
                'average_reduction_percent' => $this->averageReduction(),
                'median_reduction_percent'  => $this->medianReduction(),
                'sample_size'               => $this->reductionCount(),
                'classification'            => $this->pressureClass(),
                'summary'                   => $this->runeSummary(),
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
            'has_reductions' => $this->reductionCount() > 0,
            'sample_size'    => $this->reductionCount(),
            'variance'       => $this->reductionVariance(),
        ];
    }

    /**
     * Confidence weighting
     */
    protected function confidenceBoost(string $signal, mixed $value): float
    {
        return match ($signal) {
            'has_reductions' => $value ? 0.30 : -0.60,
            'sample_size'    => $this->sampleSizeBoost($value),
            'variance'       => $this->variancePenalty($value),
            default          => 0.0,
        };
    }

    /**
     * Core calculations
     */
    protected function reductions(): array
    {
        return array_values(
            array_filter(
                array_map(
                    static function ($comp) {
                        if (
                            empty($comp['listing_price']) ||
                            empty($comp['close_price']) ||
                            $comp['listing_price'] <= 0 ||
                            $comp['close_price'] <= 0 ||
                            $comp['close_price'] >= $comp['listing_price']
                        ) {
                            return null;
                        }

                        return round(
                            (($comp['listing_price'] - $comp['close_price'])
                                / $comp['listing_price']) * 100,
                            2
                        );
                    },
                    $this->saleComparables()
                )
            )
        );
    }

    protected function reductionCount(): int
    {
        return count($this->reductions());
    }

    protected function averageReduction(): ?float
    {
        $values = $this->reductions();

        return empty($values)
            ? null
            : round(array_sum($values) / count($values), 2);
    }

    protected function medianReduction(): ?float
    {
        $values = $this->reductions();

        if (empty($values)) {
            return null;
        }

        sort($values);
        $count = count($values);
        $mid = intdiv($count, 2);

        return $count % 2
            ? $values[$mid]
            : round(($values[$mid - 1] + $values[$mid]) / 2, 2);
    }

    protected function reductionVariance(): ?float
    {
        $values = $this->reductions();

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

    protected function pressureClass(): string
    {
        $median = $this->medianReduction();

        if (is_null($median)) {
            return 'unknown';
        }

        return match (true) {
            $median >= 10 => 'heavy',
            $median >= 5  => 'soft',
            default       => 'minimal',
        };
    }

    /**
     * Helpers
     */
    protected function saleComparables(): array
    {
        return $this->cache['payload']['comps']['sale'] ?? [];
    }

    protected function sampleSizeBoost(int $count): float
    {
        return match (true) {
            $count >= 10 => 0.25,
            $count >= 5  => 0.15,
            $count >= 3  => 0.05,
            default      => -0.20,
        };
    }

    protected function variancePenalty(?float $variance): float
    {
        if (is_null($variance)) {
            return -0.20;
        }

        return match (true) {
            $variance <= 3  => 0.15,
            $variance <= 7  => 0.05,
            default         => -0.15,
        };
    }

    public function runeSummary(): string
    {
        $count   = $this->reductionCount();
        $class   = $this->pressureClass();
        $median  = $this->medianReduction();
        $avg     = $this->averageReduction();
        $confPct = (int) round($this->runePayload()['confidence'] * 100);

        if ($count === 0 || is_null($median)) {
            return "No price reductions observed in recent comparables. | {$confPct}% Confidence";
        }

        return sprintf(
            "%s price reduction pressure with a median cut of %.2f%% (avg %.2f%%) across %d comparable%s. | %d%% Confidence",
            ucfirst($class),
            $median,
            $avg,
            $count,
            $count === 1 ? '' : 's',
            $confPct
        );
    }
}
