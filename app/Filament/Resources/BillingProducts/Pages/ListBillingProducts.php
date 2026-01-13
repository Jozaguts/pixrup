<?php

namespace App\Filament\Resources\BillingProducts\Pages;

use App\Filament\Resources\BillingProducts\BillingProductResource;
use App\Infrastructure\Billing\StripeCatalogImporter;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListBillingProducts extends ListRecords
{
    protected static string $resource = BillingProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('importAll')
                ->label('Import all from Stripe')
                ->requiresConfirmation()
                ->action(function (): void {
                    $counts = app(StripeCatalogImporter::class)->importAll();

                    Notification::make()
                        ->title('Stripe import completed')
                        ->body(sprintf(
                            'Products: +%d / ~%d, Prices: +%d / ~%d',
                            $counts['products_created'],
                            $counts['products_updated'],
                            $counts['prices_created'],
                            $counts['prices_updated'],
                        ))
                        ->success()
                        ->send();
                }),
        ];
    }
}
