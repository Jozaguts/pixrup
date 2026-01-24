<?php

namespace App\Domain\PixWorth\Runes;

use App\Domain\PixWorth\Runes\Concerns\ComputesRuneConfidence;
use App\Domain\PixWorth\Runes\Concerns\HandlesRuneDefaults;
use App\Domain\PixWorth\Runes\Concerns\HasPropertyWorth;
use App\Models\PropertyWorth;

class MarketSpreadRune
{
    use HandlesRuneDefaults;
    use HasPropertyWorth;
    use ComputesRuneConfidence;

    protected PropertyWorth $worth;

    protected function runeName(): string
    {
        return 'market_spread';
    }

    /**
     * Public output contract for PixVision
     */
    public function runePayload(): array
    {
        return [
            'rune_value'  => [
                'spread_percent' => $this->spreadPercent(),
                'classification' => $this->classification(),
                'value_low'      => $this->lowValue(),
                'value_high'     => $this->highValue(),
                'value' => $this->getValue(),
                'summary'        => $this->summary(),
            ],
            'confidence'  => $this->computeConfidence(
                $this->confidenceSignals()
            ),
        ];
    }

    /**
     * Signals used by the confidence engine
     *
     * Confidence reflects valuation certainty, not direction.
     */
    protected function confidenceSignals(): array
    {
        return [
            'has_market_data' => $this->hasMarketData(),
            'has_bounds'      => $this->hasBounds(),
            'spread_percent'  => $this->spreadPercent(),
        ];
    }

    /**
     * Signal-specific confidence modifiers
     */
    protected function confidenceBoost(string $signal, mixed $value): float
    {
        return match ($signal) {
            'has_market_data' => $value ? 0.20 : -0.30,
            'has_bounds'      => $value ? 0.20 : -0.40,
            'spread_percent'  => $this->spreadPenalty($value),
            default           => 0.0,
        };
    }

    /**
     * Penalize wide valuation ranges
     *
     * Narrow spreads imply stronger confidence.
     */
    protected function spreadPenalty(?float $spreadPercent): float
    {
        if ($spreadPercent === null) {
            return -0.30;
        }

        return match (true) {
            $spreadPercent < 5   => 0.25,   // Very tight
            $spreadPercent < 10  => 0.10,
            $spreadPercent < 20  => -0.10,
            default              => -0.30,  // Very wide
        };
    }

    /**
     * Determines market valuation tightness
     */
    protected function classification(): string
    {
        $spread = $this->spreadPercent();

        if ($spread === null) {
            return 'unknown';
        }

        return match (true) {
            $spread < 5  => 'tight',
            $spread < 15 => 'normal',
            default      => 'wide',
        };
    }

    /**
     * Calculate valuation spread as a percentage
     *
     * (high - low) / average
     */
    protected function spreadPercent(): ?float
    {
        if (! $this->hasBounds()) {
            return null;
        }

        $avg = ($this->worth->value_high + $this->worth->value_low) / 2;

        if ($avg <= 0) {
            return null;
        }

        return round(
            (($this->worth->value_high - $this->worth->value_low) / $avg) * 100,
            2
        );
    }

    /**
     * Guard: bounds exist
     */
    protected function hasBounds(): bool
    {
        return !is_null($this->worth->value_low)
            && !is_null($this->worth->value_high);
    }

    /**
     * Guard: market comps exist
     */
    protected function hasMarketData(): bool
    {
        return count($this->worth->comparables ?? []) > 0;
    }

    protected function lowValue(): ?float
    {
        return $this->worth->value_low;
    }

    protected function highValue(): ?float
    {
        return $this->worth->value_high;
    }

    protected function getValue(): ?float
    {
        return $this->worth->value;
    }

    /**
     * Human-readable interpretation of the market spread
     */
    protected function summary(): string
    {
        if (! $this->hasBounds()) {
            return 'Insufficient market data to determine valuation spread.';
        }

        $spread = $this->spreadPercent();
        $classification = $this->classification();

        return match ($classification) {
            'tight'  => sprintf(
                'Market valuation is tight with a %.2f%% spread, indicating strong price agreement.',
                $spread
            ),
            'normal' => sprintf(
                'Market valuation shows a normal spread of %.2f%%, suggesting reasonable pricing variance.',
                $spread
            ),
            'wide'   => sprintf(
                'Market valuation is wide with a %.2f%% spread, indicating higher uncertainty or mixed signals.',
                $spread
            ),
            default  => 'Market valuation spread could not be classified.',
        };
    }
}
