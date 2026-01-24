<?php

namespace App\Domain\PixWorth\Runes;
use App\Domain\PixWorth\Runes\Concerns\ComputesRuneConfidence;
use App\Domain\PixWorth\Runes\Concerns\HandlesRuneDefaults;
use App\Domain\PixWorth\Runes\Concerns\HasPropertyWorth;
use function PHPUnit\Framework\isNull;

class PriceVsMarketRune
{
    use HandlesRuneDefaults;
    use ComputesRuneConfidence;
    use HasPropertyWorth;

    protected function runeName(): string
    {
        return 'price_vs_market';
    }

    /**
     * Public output contract for PixVision
     */
    public function runePayload(): array
    {
        return [
            'rune_value'  => [
                'price'        => $this->worth->value,
                'market_low'   => $this->worth->value_low,
                'market_high'  => $this->worth->value_high,
                'deviation_pct'=> $this->priceDeviationPercent(),
                'summary'      => $this->summary(),
            ],
            'confidence'  => $this->computeConfidence(
                $this->confidenceSignals()
            ),
        ];
    }

    /**
     * Signals passed to the confidence engine
     */
    protected function confidenceSignals(): array
    {
        return [
            'has_market_data'       => $this->hasMarketData(),
            'has_property_price'    => $this->hasPropertyPrice(),
            'price_deviation_score' => $this->normalizedDeviationScore(),
        ];
    }

    protected function hasMarketData(): bool
    {
        return ! empty($this->worth->comparables);
    }

    protected function hasPropertyPrice(): bool
    {
        return is_numeric($this->worth->value);
    }

    /**
     * Raw deviation in percentage
     */
    protected function priceDeviationPercent(): ?float
    {
        if (
            ! is_numeric($this->worth->value) ||
            ! is_numeric($this->worth->value_low) ||
            ! is_numeric($this->worth->value_high)
        ) {
            return null;
        }

        $avg = ($this->worth->value_low + $this->worth->value_high) / 2;

        if ($avg <= 0) {
            return null;
        }

        return round((($this->worth->value - $avg) / $avg) * 100, 2);
    }

    /**
     * Normalized signal (0–1) for confidence weighting
     */
    protected function normalizedDeviationScore(): ?float
    {
        $deviation = abs($this->priceDeviationPercent());

        if (isNull($deviation)) {
            return null;
        }

        return match (true) {
            $deviation >= 30 => 1.0,
            $deviation >= 20 => 0.8,
            $deviation >= 10 => 0.6,
            default          => 0.4,
        };
    }

    protected function confidenceBoost(string $signal, mixed $value): float
    {
        return match ($signal) {
            'has_market_data', 'has_property_price' => $value ? 0.15 : -0.25,
            'price_deviation_strength' => $this->normalizeDeviationBoost($value),
            default => 0.0,
        };
    }
    protected function summary(): string
    {
        $price = $this->worth->value;
        $low   = $this->worth->value_low;
        $high  = $this->worth->value_high;
        $dev   = $this->priceDeviationPercent();

        if (
            ! is_numeric($price) ||
            ! is_numeric($low) ||
            ! is_numeric($high) ||
            $dev === null
        ) {
            return 'There is insufficient market data to assess how the property is priced.';
        }

        return match (true) {
            $dev > 15 =>
            "The property is priced significantly above market levels ({$dev}% above comparable values).",
            $dev > 5 =>
            "The property is priced moderately above market levels ({$dev}% above comparable values).",
            $dev >= -5 && $dev <= 5 =>
            "The property is priced in line with comparable market values.",
            $dev >= -15 =>
                "The property is priced moderately below market levels (" . abs($dev) . "% below comparable values).",
            default =>
                "The property is priced significantly below market levels (" . abs($dev) . "% below comparable values).",
        };
    }
}
