<?php

namespace App\Infrastructure\Property\Overview\Persistence;

use App\Application\Properties\Overview\Contracts\MsaMarketPulseRepository;
use App\Application\Properties\Overview\DTOs\MsaMarketPulseDTO;
use App\Models\MsaMarketPulse;
use Carbon\CarbonImmutable;

final class EloquentMsaMarketPulseRepository implements MsaMarketPulseRepository
{
    public function get(string $msa): ?MsaMarketPulseDTO
    {
        $row = MsaMarketPulse::query()
            ->where('msa', $msa)
            ->orderByDesc('fetched_at')
            ->first();

        if (!$row) {
            return null;
        }

        if ($row->expires_at && $row->expires_at->isPast()) {
            return null;
        }

        return new MsaMarketPulseDTO(
            msa: $row->msa,
            msaName: $row->msa_name,
            provider: $row->provider,
            inventoryPressure: $row->inventory_pressure,
            supplyDemand: $row->supply_demand,
            pricingMomentum: $row->pricing_momentum,
            payload: $row->payload ?? [],
            fetchedAt: CarbonImmutable::parse($row->fetched_at ?? $row->updated_at ?? 'now'),
            expiresAt: CarbonImmutable::parse($row->expires_at ?? $row->updated_at ?? 'now'),
            status: $row->status ?? 'ready',
            errorCode: $row->error_code,
            errorMessage: $row->error_message,
        );
    }

    public function save(MsaMarketPulseDTO $dto): void
    {
        MsaMarketPulse::query()->updateOrCreate(
            ['msa' => $dto->msa, 'provider' => $dto->provider],
            [
                'msa_name' => $dto->msaName,
                'inventory_pressure' => $dto->inventoryPressure,
                'supply_demand' => $dto->supplyDemand,
                'pricing_momentum' => $dto->pricingMomentum,
                'payload' => $dto->payload,
                'fetched_at' => $dto->fetchedAt->toDateTimeString(),
                'expires_at' => $dto->expiresAt->toDateTimeString(),
                'status' => $dto->status,
                'error_code' => $dto->errorCode,
                'error_message' => $dto->errorMessage,
            ]
        );
    }

    public function forget(string $msa): void
    {
        MsaMarketPulse::query()
            ->where('msa', $msa)
            ->delete();
    }
}
