<?php

namespace App\Domain\PixHunt\Runes;

use App\Domain\PixHunt\Concerns\HasSpyHuntCache;
use App\Domain\PixHunt\Concerns\HandlesRuneDefaults;
use App\Domain\PixHunt\Concerns\ComputesRuneConfidence;

class MarketSpreadRune
{
    use HandlesRuneDefaults;
    use ComputesRuneConfidence;
    use HasSpyHuntCache;

    protected function runeName(): string
    {
        return 'market_spread';
    }

    /**
     * Public PixVision payload
     */
    public function runePayload(): array
    {
        return [
            'rune_value' => [
                'value_low'      => $this->lowPrice(),
                'value_high'     => $this->highPrice(),
                'spread_percent' => $this->spreadPercent(),
                'classification' => $this->classification(),
                'comps_count'    => $this->comparablesCount(),
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
            'has_comps'      => $this->hasComparables(),
            'comps_count'   => $this->comparablesCount(),
            'spread_percent'=> $this->spreadPercent(),
        ];
    }

    protected function confidenceBoost(string $signal, mixed $value): float
    {
        return match ($signal) {
            'has_comps'       => $value ? 0.30 : -0.50,
            'comps_count'    => $this->densityBoost($value),
            'spread_percent' => $this->spreadPenalty($value),
            default           => 0.0,
        };
    }

    /**
     * Core calculations
     */
    protected function spreadPercent(): ?float
    {
        $low  = $this->lowPrice();
        $high = $this->highPrice();

        if (! is_numeric($low) || ! is_numeric($high) || $low <= 0) {
            return null;
        }

        $avg = ($low + $high) / 2;

        return round((($high - $low) / $avg) * 100, 2);
    }

    protected function classification(): string
    {
        $spread = $this->spreadPercent();

        if (is_null($spread)) {
            return 'unknown';
        }

        return match (true) {
            $spread <= 10 => 'tight',
            $spread <= 20 => 'normal',
            default       => 'wide',
        };
    }

    /**
     * Helpers
     */
    protected function hasComparables(): bool
    {
        return $this->comparablesCount() > 0;
    }

    protected function comparablesCount(): int
    {
        return count($this->saleComparables());
    }

    protected function lowPrice(): ?float
    {
        $prices = $this->comparablePrices();

        return empty($prices) ? null : min($prices);
    }

    protected function highPrice(): ?float
    {
        $prices = $this->comparablePrices();

        return empty($prices) ? null : max($prices);
    }

    protected function comparablePrices(): array
    {
        return array_values(
            array_filter(
                array_map(
                    static fn ($comp) =>
                        $comp['info']['close_price']
                        ?? $comp['info']['listing_price']
                        ?? null,
                    $this->saleComparables()
                ),
                static fn ($v) => is_numeric($v) && $v > 0
            )
        );
    }

    protected function saleComparables(): array
    {
        return $this->cache['payload']['comps']['sale'] ?? [];
    }

    /**
     * Confidence helpers
     */
    protected function densityBoost(int $count): float
    {
        return match (true) {
            $count >= 10 => 0.20,
            $count >= 5  => 0.10,
            $count >= 3  => 0.05,
            default      => -0.25,
        };
    }

    protected function spreadPenalty(?float $spread): float
    {
        if (is_null($spread)) {
            return -0.25;
        }

        return match (true) {
            $spread <= 10 => 0.20,
            $spread <= 20 => 0.05,
            default       => -0.20,
        };
    }
}
