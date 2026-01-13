<?php

namespace App\Domain\PixWorth\Jobs;

use App\Domain\PixWorth\Runes\ComparablesDensityRune;
use App\Domain\PixWorth\Runes\MarketSpreadRune;
use App\Domain\PixWorth\Runes\MarketVelocityRune;
use App\Domain\PixWorth\Runes\PriceReductionPressureRune;
use App\Domain\PixWorth\Runes\PriceVsMarketRune;
use App\Models\PixVisionPropertyRune;
use App\Models\PropertyWorth;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Str;

class GeneratePixWorthRunesJob implements ShouldQueue
{
    use Queueable;

    protected array $runes = [
        MarketVelocityRune::class,
        MarketSpreadRune::class,
        ComparablesDensityRune::class,
        PriceReductionPressureRune::class,
        PriceVsMarketRune::class,
    ];

    public function __construct(
        protected PropertyWorth $worth
    ) {}

    public function handle(): void
    {
        foreach ($this->runes as $runeClass) {
            try {
                /** @var object $rune */
                $rune = new $runeClass($this->worth);
                $data = $rune->toArray();

                PixVisionPropertyRune::upsert(
                    [[
                        'id'          => (string) Str::uuid(),
                        'property_id' => $data['property_id'],
                        'rune_key'    => $data['rune_key'],
                        'provider'    => $data['provider'], // REQUIRED
                        'version'     => $data['version'],
                        'rune_value'  => $data['rune_value'],
                        'confidence'  => $data['confidence'],
                        'computed_at' => $data['computed_at'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]],
                    ['property_id', 'rune_key', 'provider', 'version'],
                    ['rune_value', 'confidence', 'computed_at', 'updated_at']
                );

                logger()->info(
                    'Generated PixVision Rune',
                    [
                        'property_id' => $data['property_id'],
                        'rune_key'    => $data['rune_key'],
                        'provider'    => $data['provider'],
                        'version'     => $data['version'],
                    ]
                );

            } catch (\Throwable $e) {
                logger()->error(
                    'Error generating PixVision Rune',
                    [
                        'rune'       => $runeClass,
                        'property_id'=> $this->worth->property_id,
                        'error'      => $e->getMessage(),
                    ]
                );
            }
        }
    }
}
