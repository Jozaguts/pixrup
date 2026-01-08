<?php

namespace App\Filament\Resources\BillingProducts\RelationManagers;

use App\Models\BillingPrice;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PricesRelationManager extends RelationManager
{
    protected static string $relationship = 'prices';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('unit_amount')
                    ->label('Amount')
                    ->state(fn (BillingPrice $record) => self::formatPrice($record)),
                TextColumn::make('currency')
                    ->label('Currency')
                    ->toggleable(),
                TextColumn::make('type')
                    ->badge(),
                TextColumn::make('interval')
                    ->label('Interval')
                    ->toggleable(),
                TextColumn::make('active')
                    ->label('Active')
                    ->formatStateUsing(fn (bool $state) => $state ? 'Yes' : 'No'),
                TextColumn::make('stripe_price_id')
                    ->label('Stripe price')
                    ->toggleable(),
                TextColumn::make('stripe_created_at')
                    ->label('Created')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([])
            ->bulkActions([]);
    }

    private static function formatPrice(BillingPrice $price): string
    {
        $amount = number_format($price->unit_amount / 100, 2);
        $currency = strtoupper($price->currency);
        $interval = $price->interval ? '/'.$price->interval : '';

        return sprintf('%s %s%s', $currency, $amount, $interval);
    }
}
