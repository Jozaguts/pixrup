<?php

namespace App\Application\Properties\Overview\UseCases;

use App\Application\Properties\Overview\Contracts\OverviewProvider;
use App\Application\Properties\Overview\Contracts\OverviewRepository;
use App\Application\Properties\Overview\Contracts\MsaMarketPulseRepository;
use App\Domain\Properties\Entities\PropertyEntity;

final readonly class CreatePropertyOverviewUseCase
{
    public function __construct(
        private OverviewRepository $overviewRepository,
        private MsaMarketPulseRepository $msaRepository,
        private OverviewProvider $provider,
    ) {}

    /** @return array<string, mixed> */
    public function execute(PropertyEntity $propertyEntity): array
    {
        if ($propertyEntity->id === null) {
            throw new \InvalidArgumentException('Property id is required for overview.');
        }

        $snapshot = $this->overviewRepository->get($propertyEntity->id);
        if (!$snapshot) {
            $snapshot = $this->provider->fetchPropertySnapshot($propertyEntity);
            $this->overviewRepository->save($snapshot);
        }

        $msaMarketPulse = null;
        // Temporarily disabled: our plan doesn't include MSA market pulse access yet.
        // $msa = $snapshot->msa ?? data_get($snapshot->payload, 'census.msa');
        // if ($msa) {
        //     $msaMarketPulse = $this->msaRepository->get($msa);
        //     if (!$msaMarketPulse) {
        //         $msaMarketPulse = $this->provider->fetchMsaMarketPulse($msa);
        //         $this->msaRepository->save($msaMarketPulse);
        //     }
        // }

        return [
            'snapshot' => $snapshot->toArray(),
            'msa_market_pulse' => $msaMarketPulse?->toArray(),
        ];
    }
}
