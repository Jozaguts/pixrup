<?php

namespace App\Infrastructure\PixVision\UseCases;

use App\Application\PixVision\Extractors\PropertyOverviewDataExtractor;
use App\Application\PixVision\Extractors\RuneDataExtractor;
use App\Infrastructure\Shared\LocalImageToBase64;
use App\Infrastructure\Shared\S3ImageToBase64;
use App\Models\GlowupJob;
use App\Models\PixVisionPropertyRune;
use App\Models\Property;
use App\Models\PropertyOverview;
use App\Models\SpyHuntCache;
use Illuminate\Support\Collection;

class GeneratePixVisionReport
{
    protected mixed $propertyId;

    protected ?PropertyOverview $overview = null;
    public function __construct(public Property $property)
    {
        $this->propertyId = $property->id;
    }

    public function execute():array
    {
        return [
            'glow_up' => $this->getGlowUpJobs(),
            'logo' => $this->getLogoString(),
            'icon' =>  $this->getWarningIconString(),
            'property_image' => $this->getPropertyImage(),
            'spy_hunt_cache' => $this->getSpyHuntCache(),
            'overview' => $this->getPropertyOverview(),
            'runes' => $this->getPropertyRunes(),
            'property' => $this->property,
            'sales_history' => $this->getSalesHistory(),
            'worth' => $this->property->latestWorth
        ];
    }
    protected function getPropertyImage(): string|null
    {
        $path = $this->property->photos()->first()?->path;

        return $path ? S3ImageToBase64::execute($path) : null;
    }

    protected function getSpyHuntCache(): array|null
    {
        $spyHuntCache = SpyHuntCache::where('property_id',$this->propertyId)
            ->orderBy('created_at','desc')
            ->first();

        return $spyHuntCache ? $spyHuntCache->payload : null;
    }

    protected function getPropertyOverview(): array|null
    {
        $overview = $this->getPropertyOverviewModel();
        if($overview){
            $overview = (new PropertyOverviewDataExtractor)
                ->getInvestmentSignals($overview);
        }

        return $overview;
    }

    protected function getLogoString(): string
    {
        return LocalImageToBase64::execute('images/pixrup.png');
    }

    protected function getWarningIconString(): string
    {
        return LocalImageToBase64::execute('images/warning.png');
    }

    protected function getGlowUpJobs(): Collection
    {
        return $this->property?->glowupJobs;
    }

    protected function getPropertyRunes(): array
    {
       return RuneDataExtractor::extract($this->property->id);
    }

    protected function getSalesHistory(): array {

        $overview = $this->getPropertyOverviewModel();

        if(!$overview) {
            return [];
        }

        return (new PropertyOverviewDataExtractor)
            ->getSalesHistory($overview);
    }


    protected function getPropertyOverviewModel() {
        if(!$this->overview) {
         $this->overview = PropertyOverview::where('property_id',$this->property->id)
            ->orderBy('created_at','desc')
            ->first();
         }

        return $this->overview;
    }
}
