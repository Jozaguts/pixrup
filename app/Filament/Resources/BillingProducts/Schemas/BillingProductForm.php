<?php

namespace App\Filament\Resources\BillingProducts\Schemas;

use App\Models\BillingProduct;
use App\Models\BillingPrice;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class BillingProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('key')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->disabled(fn (?BillingProduct $record) => !empty($record?->stripe_product_id))
                    ->helperText('Editable only for local products without a Stripe ID.'),

                TextInput::make('name')
                    ->required(),

                Textarea::make('description')
                    ->rows(3),

                Select::make('type')
                    ->options([
                        'subscription' => 'Subscription',
                        'one_time' => 'One time',
                    ])
                    ->required(),

                Toggle::make('is_active')
                    ->default(true),

                TextInput::make('sort_order')
                    ->numeric()
                    ->default(0),

                TextInput::make('stripe_product_id')
                    ->disabled()
                    ->dehydrated(false),

                TextInput::make('stripe_default_price_id')
                    ->label('Stripe default price')
                    ->disabled()
                    ->dehydrated(false),

                Select::make('primary_price_id')
                    ->label('Primary price')
                    ->options(fn (?BillingProduct $record) => self::priceOptions($record))
                    ->searchable()
                    ->placeholder('Select a primary price'),
            ]);
    }

    /** @return array<string, string> */
    private static function priceOptions(?BillingProduct $record): array
    {
        if (!$record) {
            return [];
        }

        return $record->prices()
            ->orderByDesc('active')
            ->orderBy('unit_amount')
            ->get()
            ->mapWithKeys(fn (BillingPrice $price) => [
                $price->stripe_price_id => self::formatPriceLabel($price),
            ])
            ->toArray();
    }

    private static function formatPriceLabel(BillingPrice $price): string
    {
        $amount = number_format($price->unit_amount / 100, 2);
        $currency = strtoupper($price->currency);
        $interval = $price->interval ? '/'.$price->interval : '';
        $state = $price->active ? 'active' : 'inactive';

        return sprintf('%s %s%s (%s)', $currency, $amount, $interval, $state);
    }
}
