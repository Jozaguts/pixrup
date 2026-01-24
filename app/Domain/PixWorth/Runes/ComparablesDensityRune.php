<?php

namespace App\Domain\PixWorth\Runes;

use App\Domain\PixWorth\Runes\Concerns\ComputesRuneConfidence;
use App\Domain\PixWorth\Runes\Concerns\HandlesRuneDefaults;
use App\Domain\PixWorth\Runes\Concerns\HasPropertyWorth;

class ComparablesDensityRune
{
    use HandlesRuneDefaults;
    use ComputesRuneConfidence;
    use HasPropertyWorth;

    protected function runeName(): string
    {
        return 'comps_density';
    }
    /**
     * Public PixVision contract
     */
    public function runePayload(): array
    {
        return [
            'rune_value'  => [
                'count'          => $this->comparablesCount(),
                'classification' => $this->classification(),
                'summary'        => $this->summary(),
            ],
            'confidence'  => $this->computeConfidence(
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
            'has_comps'    => $this->hasMarketData(),
            'comps_count'  => $this->comparablesCount(),
        ];
    }

    /**
     * Signal confidence weighting
     */
    protected function confidenceBoost(string $signal, mixed $value): float
    {
        return match ($signal) {
            'has_comps'   => $value ? 0.25 : -0.50,
            'comps_count'=> $this->normalizeDeviationBoost($value),
            default       => 0.0,
        };
    }

    protected function classification(): string
    {
        $count = $this->comparablesCount();

        return match (true) {
            $count >= 12 => 'strong',
            $count >= 7  => 'solid',
            $count >= 2  => 'light',
            default      => 'weak',
        };
    }

    /**
     * Guards
     */
    protected function hasMarketData(): bool
    {
        return $this->comparablesCount() > 0;
    }

    protected function comparablesCount(): int
    {
        return count($this->worth->comparables ?? []);
    }

    protected function summary(): string
    {
        $count = $this->comparablesCount();

        return match ($this->classification()) {
            'strong' => "Strong comparable density based on {$count} properties, providing high valuation confidence.",
            'solid'  => "Solid comparable density based on {$count} properties, offering reasonable market support.",
            'light'  => "Light comparable density with only {$count} properties, reducing valuation certainty.",
            'weak'   => 'Weak comparable density due to insufficient market data.',
        };
    }
}
