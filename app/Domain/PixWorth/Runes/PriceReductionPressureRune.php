<?php

namespace App\Domain\PixWorth\Runes;

use App\Domain\PixWorth\Runes\Concerns\ComputesRuneConfidence;
use App\Domain\PixWorth\Runes\Concerns\HandlesRuneDefaults;
use App\Domain\PixWorth\Runes\Concerns\HasPropertyWorth;

class PriceReductionPressureRune
{
    use HandlesRuneDefaults;
    use HasPropertyWorth;
    use ComputesRuneConfidence;

    protected function runeName(): string
    {
        return 'price_reduction_pressure';
    }

    /**
     * Public PixWorth contract
     */
    public function runePayload(): array
    {
        return [
            'rune_value' => [
                'average_reduction_percent' => $this->averageReductionPercent(),
                'median_reduction_percent'  => $this->medianReductionPercent(),
                'sample_size'               => $this->validReductionCount(),
                'classification'            => $this->classification(),
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
            'has_closed_comps' => $this->hasClosedComps(),
            'sample_size'      => $this->validReductionCount(),
        ];
    }

    /**
     * Confidence modifiers
     */
    protected function confidenceBoost(string $signal, mixed $value): float
    {
        return match ($signal) {
            'has_closed_comps' => $value ? 0.30 : -0.50,
            'sample_size'      => $this->sampleSizeBoost($value),
            default            => 0.0,
        };
    }

    protected function sampleSizeBoost(int $count): float
    {
        return match (true) {
            $count >= 12 => 0.30,
            $count >= 8  => 0.20,
            $count >= 4  => 0.10,
            default      => -0.25,
        };
    }

    /**
     * Pressure classification
     */
    protected function classification(): string
    {
        $avg = $this->averageReductionPercent();

        if ($avg === null) {
            return 'unknown';
        }

        return match (true) {
            $avg <= 2  => 'strong',
            $avg <= 5  => 'balanced',
            $avg <= 10 => 'soft',
            default    => 'pressured',
        };
    }

    /**
     * Average price reduction
     */
    protected function averageReductionPercent(): ?float
    {
        $values = $this->reductionPercentValues();

        if (empty($values)) {
            return null;
        }

        return round(array_sum($values) / count($values), 2);
    }

    /**
     * Median price reduction
     */
    protected function medianReductionPercent(): ?float
    {
        $values = $this->reductionPercentValues();

        if (empty($values)) {
            return null;
        }

        sort($values);
        $count = count($values);
        $mid = intdiv($count, 2);

        return $count % 2 === 0
            ? round(($values[$mid - 1] + $values[$mid]) / 2, 2)
            : $values[$mid];
    }

    /**
     * Extract reduction percentages from comps
     */
    protected function reductionPercentValues(): array
    {
        return array_values(
            array_filter(
                array_map(
                    fn ($comp) => $this->reductionFromComp($comp),
                    $this->worth->comparables ?? []
                ),
                fn ($value) => $value !== null
            )
        );
    }

    /**
     * Reduction for a single comparable
     */
    protected function reductionFromComp(array $comp): ?float
    {
        $info = $comp['info'] ?? null;

        if (
            !$info ||
            ($info['status'] ?? null) !== 'Closed' ||
            empty($info['listing_price']) ||
            empty($info['close_price'])
        ) {
            return null;
        }

        if ($info['listing_price'] <= 0) {
            return null;
        }

        return round(
            (($info['listing_price'] - $info['close_price']) / $info['listing_price']) * 100,
            2
        );
    }

    /**
     * Guards
     */
    protected function hasClosedComps(): bool
    {
        return $this->validReductionCount() > 0;
    }

    protected function validReductionCount(): int
    {
        return count($this->reductionPercentValues());
    }
}
