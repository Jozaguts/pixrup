<?php
namespace App\Domain\PixWorth\Runes\Concerns;

trait ComputesRuneConfidence {
    /**
        * Compute confidence score for a rune.
        *
        * This is a heuristic-based approach that can evolve into
        * weighted signals, data density checks, or ML outputs.
    */
    protected function computeConfidence(array $signals = []): float
    {
        $baseConfidence = 0.6;

        foreach ($signals as $signal => $value) {
            $baseConfidence += $this->confidenceBoost($signal, $value);
        }

        return round(
            max(0.0, min(1.0, $baseConfidence)),
            2
        );
    }

    /**
     * Apply confidence adjustments based on signal quality
     */
    abstract protected function confidenceBoost(string $signal, mixed $value): float;

    protected function normalizeDeviationBoost(float $deltaPercent): float
    {
        $abs = abs($deltaPercent);

        return match (true) {
            $abs >= 20 => 0.15,
            $abs >= 10 => 0.10,
            $abs >= 5  => 0.05,
            default    => 0.0,
        };
    }
}
