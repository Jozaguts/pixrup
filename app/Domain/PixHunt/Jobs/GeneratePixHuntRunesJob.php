<?php

namespace App\Domain\PixHunt\Jobs;

use App\Domain\PixHunt\Runes\ComparablesDensityRune;
use App\Domain\PixHunt\Runes\MarketSpreadRune;
use App\Domain\PixHunt\Runes\MarketVelocityRune;
use App\Domain\PixHunt\Runes\PriceReductionPressureRune;
use App\Domain\PixHunt\Runes\PriceVsMarketRune;
use App\Models\PixVisionPropertyRune;
use App\Models\SpyHuntCache;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Str;

class GeneratePixHuntRunesJob implements ShouldQueue
{
    use Queueable;

    protected array $runes = [
        ComparablesDensityRune::class,
        MarketVelocityRune::class,
        MarketSpreadRune::class,
        PriceReductionPressureRune::class,
        PriceVsMarketRune::class,
    ];

    public function __construct(
        protected SpyHuntCache $cache
    )
    {
    }

    public function handle(): void
    {
        foreach ($this->runes as $runeClass) {
            try {
                /** @var object $rune */
                $rune = new $runeClass($this->cache);
                $data = $rune->toArray();

                PixVisionPropertyRune::upsert(
                    [[
                        'id' => (string)Str::uuid(),
                        'property_id' => $data['property_id'],
                        'rune_key' => $data['rune_key'],
                        'provider' => $data['provider'], // REQUIRED
                        'version' => $data['version'],
                        'rune_value' => $data['rune_value'],
                        'confidence' => $data['confidence'],
                        'computed_at' => $data['computed_at'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]],
                    ['property_id', 'rune_key', 'provider', 'version'],
                    ['rune_value', 'confidence', 'computed_at', 'updated_at']
                );

                $this->onSuccess($data, $runeClass);

            } catch (\Throwable $e) {
                $this->onError($e, $runeClass);
            }
        }
    }


    protected function onError(\Throwable $exception, string $rune): void
    {
        $propertyId = $this->cache->property_id ?? 'unknown';
        logger()?->error(
            'Job failed: ' . $exception->getMessage(),
            [
                'property_id' => $propertyId,
                'rune' => $rune
            ]
        );
    }


    protected function onSuccess(array $payload, string $rune): void {
        logger()?->info(
            'Generated PixVision Rune',
            [
                'property_id' => $payload['property_id'],
                'rune_key' => $payload['rune_key'],
                'provider' => $payload['provider'],
                'version' => $payload['version'],
            ]
        );
    }
}
