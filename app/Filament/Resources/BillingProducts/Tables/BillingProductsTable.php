<?php

namespace App\Filament\Resources\BillingProducts\Tables;

use App\Infrastructure\Billing\StripeCatalogImporter;
use App\Models\BillingPrice;
use App\Models\BillingProduct;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use  Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class BillingProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('key')
                    ->toggleable(),

                TextColumn::make('type')
                    ->badge()
                    ->sortable(),

                ToggleColumn::make('is_active'),

                TextColumn::make('primary_price')
                    ->label('Primary price')
                    ->state(fn (BillingProduct $record) => self::formatPrice($record->primaryPrice()))
                    ->toggleable(),

                TextColumn::make('stripe_product_id')
                    ->label('Stripe product')
                    ->toggleable(),
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('sync')
                    ->label('Sync from Stripe')
                    ->requiresConfirmation()
                    ->disabled(fn (BillingProduct $record) => empty($record->stripe_product_id))
                    ->action(function (BillingProduct $record): void {
                        $counts = app(StripeCatalogImporter::class)->importProduct($record->stripe_product_id);

                        Notification::make()
                            ->title('Stripe sync completed')
                            ->body(self::formatCounts($counts))
                            ->success()
                            ->send();
                    }),
            ])
            ->toolbarActions([]);
    }

    private static function formatPrice(?BillingPrice $price): string
    {
        if (!$price) {
            return '-';
        }

        $amount = number_format($price->unit_amount / 100, 2);
        $currency = strtoupper($price->currency);
        $interval = $price->interval ? '/'.$price->interval : '';

        return sprintf('%s %s%s', $currency, $amount, $interval);
    }

    /** @param array<string, int> $counts */
    private static function formatCounts(array $counts): string
    {
        return sprintf(
            'Products: +%d / ~%d, Prices: +%d / ~%d',
            $counts['products_created'],
            $counts['products_updated'],
            $counts['prices_created'],
            $counts['prices_updated'],
        );
    }
}
