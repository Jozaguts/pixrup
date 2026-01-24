<?php

namespace App\Application\PixVision\Extractors;

use App\Models\PixVisionPropertyRune;
use App\Models\Property;
use App\Models\PropertyOverview;
use App\View\Components\rune;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use function PHPUnit\Framework\isArray;
use function Symfony\Component\Translation\t;

class RuneDataExtractor
{
    protected static array $runes;

    protected static function init(string $propertyId): void {
        self::$runes = PixVisionPropertyRune::where('property_id', $propertyId)
            ->get()
            ->toArray();
    }

    public static function extract(string $propertyId): array {
        self::init($propertyId);//load runes
        $parsedRunes = [];
        foreach(self::$runes as $rune) {
            $parsedRune = self::parseRune($rune);
            $key = $parsedRune['key'];
            $provider = $parsedRune['provider'];
            $parsedRunes[$provider][$key] =  self::parseRune($rune);
        }

        return $parsedRunes;
    }

    /**
     * @throws \JsonException
     */
    protected static function parseRune(array $data): array {
        $data['key']  = $data['rune_key'];
        $data['name'] = Str::upper(str_replace('_', ' ',$data['rune_key']));
        if(!is_array($data['rune_value'])) {
            $data['rune_value'] = json_decode($data['rune_value'], true, 512, JSON_THROW_ON_ERROR) ?? [];
        }
        $parsedRune = [
            'key' => $data['rune_key'],
            ...$data['rune_value'],
            ...$data
        ];
        if($parsedRune['provider'] === 'pix_glow_up') {
            $parsedRune['items'] = self::generateGlowUpRuneSummary($data);
        }
        unset($data['rune_value'],$data['rune_key']); //remove unneeded keys

        return $parsedRune;
    }

    protected static function generateGlowUpRuneSummary(array $rune): array {
        $defects = $rune['rune_value']['defects'] ?? [];
        $items = [];

        foreach($defects as $defectKey => $defect) {
            if(!$defect['detected']) {
                continue;
                //skip undetected defects
            }

            $runeKey = ucfirst(str_replace('_', ' ', $defectKey));
            $runeSeverity = strtoupper($defect['severity']);
            $summaryString = $runeKey .  ' detected. Damage type: ' . $runeSeverity;
            $confidence =(int) round($defect['confidence'], 2);
            $items[] = [
                'summary' => $summaryString,
                'confidence' => $confidence,
            ];
        }

        return $items;
    }
}


