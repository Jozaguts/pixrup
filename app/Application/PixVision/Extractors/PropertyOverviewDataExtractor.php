<?php

namespace App\Application\PixVision\Extractors;

use App\Models\Property;
use App\Models\PropertyOverview;
use function PHPUnit\Framework\isArray;

class PropertyOverviewDataExtractor
{
    protected function ownerOccupied(array $payload): array
    {
        $isOwnerOccupied = $payload['owner_occupied'];

        return [
            'value' => $this->boolToString((bool) $isOwnerOccupied),
            'label' => 'Owner Occupied',
            'detail' => is_null($isOwnerOccupied) ? 'Status not reported' : null
        ];
    }

    protected function femaDetails(array $columns): string
    {
        $femaCount = $payload['hazards']['fema_disaster_area']['details'] ?? [];

        return $femaCount > 0
            ? $femaCount . ' declarations'
            : 'No declarations';
    }

    protected function femaDisasterArea(array $columns): array
    {
        $femaDisasterArea = $columns['fema_disaster_area'] ?? null;
        return [
             'label' => 'FEMA Disaster Area',
             'value' => $femaDisasterArea ?: 'N/A',
             'detail' => $femaDisasterArea ? $this->femaDetails($columns) : '__',
        ];
    }

    protected function floodZone(array $columns): array
    {
        $floodRisk = $payload['flood_risk'] ?? false;
        $floodZone = $payload['flood_zone'] ?? false;
        return [
            'label'=> 'Flood zone',
            'value' => $this->boolToString((bool) $floodZone),
            'detail' => $floodRisk ? "Risk: $floodRisk" : 'Risk: -',
        ];
    }

    protected function crime(PropertyOverview $overview): array
    {
        $payload = $overview->payload;
        $percentile = $overview->crime_percentile ?? null;
        $scope = $overview->crime_compare_scope ?? null;
        return [
            'label' => 'Crime percentile',
            'value' => !is_null($percentile) ? $percentile.'th' : '—',
            'detail' => $this->crimeDetail($payload, $scope),
        ];
    }

    protected function crimeDetail(array $payload, mixed $crimeScope): string
    {
        $crimeScope = $crimeScope ? ucfirst($crimeScope) : null;
       $crime = $payload['block_crime'] ?? [];
       $crimeStats = $crime['property'] ?? $crime['all'] ?? [];
       $incidents = $crimeStats['incidents'] ?? null;
       $parts = [];
        if ($crimeScope) {
            $parts[] = "$crimeScope scope";
        }
        if (!is_null($incidents)) {
            $parts[] ="$incidents incidents";
        }

        return count($parts) > 0 ? implode(' | ', $parts) : 'No crime data available';
    }

    public function getInvestmentSignals(PropertyOverview $overview):array {
        $payload = $overview->payload;
        return [
          'owner_occupied' => $this->ownerOccupied($payload),
          'fema_disaster_area' => $this->femaDisasterArea($payload),
            'flood_zone' => $this->floodZone($payload),
            'crime_percentile' => $this->crime($overview),
            'msa' => $this->msa($overview),
            'census' => $this->census($payload),
            'census_track' => $this->censusTrack($overview),
            'block_group' => $this->blockGroup($overview),
        ];
    }

    protected function boolToString(?bool $value): string
    {
        return $value ? 'Yes' : 'No';
    }

    protected function msa(PropertyOverview $overview):array {
        return [
            'label' => 'Location Context: MSA',
            'value' => $overview->msa,
            'detail' => $overview->msa_name,
        ];
    }

    protected function census(array $payload):array {
        $census = $payload['census'];
        return [
            'label' => 'Location Context: County',
            'value' => $census['county_name'] ?? '-',
            'detail' => isset($census['fips']) ? 'FIPS ' . $census['fips'] : null,
        ];
    }

    protected function censusTrack(PropertyOverview $overview):array {
        return [
            'label' => 'Location Context: Census tract',
            'value' => $overview->census_tract,
            'detail' => ''
        ];
    }

    protected function blockGroup(PropertyOverview $overview):array {
        return [
            'label' => 'Location Context: Block group',
            'value' => $overview->block_group,
            'detail' => ''
        ];
    }

    public function getSalesHistory(PropertyOverview $overview): array
    {
        $sales = [];
        $salesHistory = $overview->payload['sales_history'] ?? [];
        foreach($salesHistory as $sale) {
            try {
                $eventType = ucwords(str_replace('_', ' ', $sale['event_type'])) ?? 'Unknown Event';
                $buyerName = ($sale['grantee_1_forenames'] . ' ' . $sale['grantee_1']) ?? 'Unknown Buyer';
                $sellerName = ($sale['grantor_1_forenames'] . ' ' . $sale['grantor_1']) ?? 'Unknown Seller';
                $sales[] = [
                    'label' =>  $eventType,
                    'buyer' => $buyerName,
                    'seller' => $sellerName,
                    'detail' => $sale['record_date'] ?? '-'
                ];
            }
            catch(\Exception $e) {
                // Skip malformed sale entries
                continue;
            }
        }

        return $sales;
    }

}


