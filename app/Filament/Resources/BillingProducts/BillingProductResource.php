<?php

namespace App\Filament\Resources\BillingProducts;

use App\Filament\Resources\BillingProducts\Pages\EditBillingProduct;
use App\Filament\Resources\BillingProducts\Pages\ListBillingProducts;
use App\Filament\Resources\BillingProducts\Schemas\BillingProductForm;
use App\Filament\Resources\BillingProducts\Tables\BillingProductsTable;
use App\Filament\Resources\BillingProducts\RelationManagers\PricesRelationManager;
use App\Models\BillingProduct;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class BillingProductResource extends Resource
{
    protected static ?string $model = BillingProduct::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCreditCard;

    protected static string|null|\UnitEnum $navigationGroup = 'Billing';

    public static function form(Schema $schema): Schema
    {
        return BillingProductForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BillingProductsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            PricesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBillingProducts::route('/'),
            'edit' => EditBillingProduct::route('/{record}/edit'),
        ];
    }
}
