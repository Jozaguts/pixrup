<?php

namespace App\Domain\PixHunt\Runes;

use App\Domain\PixHunt\Concerns\HasSpyHuntCache;
use App\Domain\PixHunt\Concerns\HandlesRuneDefaults;
use App\Domain\PixHunt\Concerns\ComputesRuneConfidence;

class PriceVsMarketRune
{
    use HandlesRuneDefaults;
    use ComputesRuneConfidence;
    use HasSpyHuntCache;

    protected function runeName(): string
    {
        return 'price_vs_market';
    }

    /**
     * Public PixVision payload
     */
    public function runePayload(): array
    {
        return [
            'rune_value' => [
                'subject_price' => $this->subjectPrice(),
                'market_median' => $this->marketMedian(),
                'deviation_pct' => $this->deviationPercent(),
                'classification'=> $this->pricePosition(),
                'summary' => $this->runeSummary(),
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
            'has_subject_price' => is_numeric($this->subjectPrice()),
            'has_market_data'   => is_numeric($this->marketMedian()),
            'comps_count'       => $this->compsCount(),
            'deviation_strength'=> abs($this->deviationPercent() ?? 0),
        ];
    }

    /**
     * Confidence weighting
     */
    protected function confidenceBoost(string $signal, mixed $value): float
    {
        return match ($signal) {
            'has_subject_price',
            'has_market_data' => $value ? 0.25 : -0.60,

            'comps_count' => $this->densityBoost($value),

            'deviation_strength' => $this->deviationBoost($value),

            default => 0.0,
        };
    }

    /**
     * Core calculations
     */
    protected function subjectPrice(): ?float
    {
        return $this->cache['payload']['subject']['price'] ?? null;
    }

    protected function marketMedian(): ?float
    {
        $prices = array_values(
            array_filter(
                array_map(
                    static fn ($comp) => $comp['close_price'] ?? null,
                    $this->saleComparables()
                ),
                static fn ($v) => is_numeric($v) && $v > 0
            )
        );

        if (empty($prices)) {
            return null;
        }

        sort($prices);
        $count = count($prices);
        $mid = intdiv($count, 2);

        return $count % 2
            ? $prices[$mid]
            : round(($prices[$mid - 1] + $prices[$mid]) / 2, 2);
    }

    protected function deviationPercent(): ?float
    {
        $subject = $this->subjectPrice();
        $market  = $this->marketMedian();

        if (! is_numeric($subject) || ! is_numeric($market) || $market <= 0) {
            return null;
        }

        return round((($subject - $market) / $market) * 100, 2);
    }

    protected function pricePosition(): string
    {
        $deviation = $this->deviationPercent();

        if (is_null($deviation)) {
            return 'unknown';
        }

        return match (true) {
            $deviation >= 10  => 'overpriced',
            $deviation <= -10 => 'underpriced',
            default           => 'at_market',
        };
    }

    /**
     * Helpers
     */
    protected function saleComparables(): array
    {
        return $this->cache['payload']['comps']['sale'] ?? [];
    }

    protected function compsCount(): int
    {
        return count($this->saleComparables());
    }

    protected function densityBoost(int $count): float
    {
        return match (true) {
            $count >= 10 => 0.25,
            $count >= 5  => 0.15,
            $count >= 3  => 0.05,
            default      => -0.20,
        };
    }

    protected function deviationBoost(float $absDeviation): float
    {
        return match (true) {
            $absDeviation >= 20 => 0.20,
            $absDeviation >= 10 => 0.10,
            default             => 0.0,
        };
    }

    public function runeSummary(): string
    {
        $subject = $this->subjectPrice();
        $market  = $this->marketMedian();
        $dev     = $this->deviationPercent();
        $class   = $this->pricePosition();
        $count   = $this->compsCount();
        $confPct = (int) round($this->runePayload()['confidence'] * 100);

        if (! is_numeric($subject) || ! is_numeric($market) || is_null($dev)) {
            return "Insufficient market data to assess price positioning. | {$confPct}% Confidence";
        }
        $direction = match(true) {
            $dev > 0  => 'above',
            $dev < 0  => 'below',
            default   => 'at',
        };

        $absDev    = abs($dev);

        return sprintf(
            "Subject is %s market pricing (%s) by %.2f%% based on %d comparable%s. | %d%% Confidence",
            $direction,
            str_replace('_', ' ', $class),
            $absDev,
            $count,
            $count === 1 ? '' : 's',
            $confPct
        );
    }
}
