<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->disabled()
                    ->dehydrated(false),

                TextInput::make('email')
                    ->disabled()
                    ->dehydrated(false),

                Select::make('role')
                    ->options([
                        'default' => 'Default',
                        'admin' => 'Admin',
                    ])
                    ->required(),
            ]);
    }
}
