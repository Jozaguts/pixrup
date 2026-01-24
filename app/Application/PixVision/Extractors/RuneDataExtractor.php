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

    protected static function parseRune(array $data): array {
        $data['key']  = $data['rune_key'];
        $data['name'] = Str::upper(str_replace('_', ' ',$data['rune_key']));
        $parsedRune = [
            'key' => $data['rune_key'],
            ...$data['rune_value'],
            ...$data
        ];
        if($parsedRune['provider'] === 'pix_glow_up') {
            $parsedRune['key'] = self::generateGlowUpRuneKey($data);
            $parsedRune['items'] = self::generateGlowUpRuneSummary($data);
        }
        unset($data['rune_value'],$data['rune_key']); //remove unneeded keys

        return $parsedRune;
    }

    protected static function generateGlowUpRuneKey(array $rune): string {
        $key = data_get($rune, 'rune_value.glow_up_job_id');
        $runeName = explode('_', $rune['rune_key']);
        array_pop($runeName);

        $runeName[] = $key;



        return implode('_', $runeName);
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
            $confidence =(int) (round($defect['confidence'], 2) * 100);
            $items[] = [
                'summary' => $summaryString,
                'confidence' => $confidence,
            ];
        }

        return $items;
    }
}


