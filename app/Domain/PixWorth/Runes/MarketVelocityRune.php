<?php

namespace App\Domain\PixWorth\Runes;

use App\Domain\PixWorth\Runes\Concerns\ComputesRuneConfidence;
use App\Domain\PixWorth\Runes\Concerns\HandlesRuneDefaults;
use App\Domain\PixWorth\Runes\Concerns\HasPropertyWorth;
use Carbon\Carbon;

class MarketVelocityRune
{
    use HandlesRuneDefaults;
    use ComputesRuneConfidence;
    use HasPropertyWorth;

    /**
     * Rune identifier
     */
    protected function runeName(): string
    {
        return 'market_velocity';
    }

    /**
     * Public PixWorth payload
     */
    public function runePayload(): array
    {
        return [
            'rune_value' => [
                'average_days_on_market' => $this->averageDaysOnMarket(),
                'median_days_on_market'  => $this->medianDaysOnMarket(),
                'sample_size'            => $this->validCompsCount(),
                'classification'         => $this->classification(),
                'summary'                => $this->summary(),
            ],
            'confidence' => $this->computeConfidence(
                $this->confidenceSignals()
            ),
        ];
    }

    /**
     * Confidence input signals
     */
    protected function confidenceSignals(): array
    {
        return [
            'has_closed_comps' => $this->hasClosedComps(),
            'sample_size'      => $this->validCompsCount(),
        ];
    }

    /**
     * Confidence weighting logic
     */
    protected function confidenceBoost(string $signal, mixed $value): float
    {
        return match ($signal) {
            'has_closed_comps' => $value ? 0.30 : -0.50,
            'sample_size'      => $this->sampleSizeBoost($value),
            default            => 0.0,
        };
    }

    /**
     * Sample size → confidence mapping
     */
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
     * Market velocity classification
     */
    protected function classification(): string
    {
        $avg = $this->averageDaysOnMarket();

        if ($avg === null) {
            return 'unknown';
        }

        return match (true) {
            $avg <= 20  => 'very_fast',
            $avg <= 40  => 'fast',
            $avg <= 70  => 'normal',
            $avg <= 100 => 'slow',
            default     => 'very_slow',
        };
    }

    /**
     * Average Days on Market
     */
    protected function averageDaysOnMarket(): ?float
    {
        $days = $this->daysOnMarketValues();

        if (empty($days)) {
            return null;
        }

        return round(array_sum($days) / count($days), 1);
    }

    /**
     * Median Days on Market
     */
    protected function medianDaysOnMarket(): ?float
    {
        $days = $this->daysOnMarketValues();

        if (empty($days)) {
            return null;
        }

        sort($days);
        $count = count($days);
        $mid = intdiv($count, 2);

        return $count % 2 === 0
            ? round(($days[$mid - 1] + $days[$mid]) / 2, 1)
            : $days[$mid];
    }

    /**
     * Extract valid Days on Market values
     */
    protected function daysOnMarketValues(): array
    {
        return array_values(
            array_filter(
                array_map(
                    fn ($comp) => $this->daysOnMarketFromComp($comp),
                    $this->worth->comparables ?? []
                ),
                static fn ($days) => $days !== null
            )
        );
    }

    /**
     * Calculate DOM for a single comparable
     */
    protected function daysOnMarketFromComp(array $comp): ?int
    {
        $info = $comp['info'] ?? null;

        if (
            !$info ||
            ($info['status'] ?? null) !== 'Closed' ||
            empty($info['listing_date']) ||
            empty($info['close_date'])
        ) {
            return null;
        }

        try {
            $listed = Carbon::parse($info['listing_date']);
            $closed = Carbon::parse($info['close_date']);

            if ($closed->lessThanOrEqualTo($listed)) {
                return null;
            }

            return $listed->diffInDays($closed);
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * Guards
     */
    protected function hasClosedComps(): bool
    {
        return $this->validCompsCount() > 0;
    }

    protected function validCompsCount(): int
    {
        return count($this->daysOnMarketValues());
    }

    protected function summary(): string
    {
        $classification = $this->classification();
        $avg = $this->averageDaysOnMarket();
        $median = $this->medianDaysOnMarket();
        $count = $this->validCompsCount();

        if ($classification === 'unknown') {
            return 'There was insufficient closed sale data to evaluate how quickly properties are selling.';
        }

        return match ($classification) {
                'very_fast' => 'Properties in this market are selling extremely quickly.',
                'fast'      => 'Properties in this market are selling faster than average.',
                'normal'    => 'Properties in this market are selling at a typical pace.',
                'slow'      => 'Properties in this market are taking longer to sell.',
                'very_slow' => 'Properties in this market are selling very slowly.',
        };
    }
}
