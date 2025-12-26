<?php

namespace App\Filament\Resources\Features\Schemas;

use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class FeatureForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, Set $set) =>
                    $set('slug', Str::slug($state))
                    ),

                TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true),

                Textarea::make('excerpt')
                    ->rows(3),
                Toggle::make('is_active')
                    ->default(true),
                RichEditor::make('description')
                    ->columnSpanFull(),
                TextInput::make('icon')
                    ->placeholder('heroicon-o-cog / ruta svg'),
                TextInput::make('sort_order')
                    ->numeric()
                    ->default(0),

                Builder::make('content')
                    ->columnSpanFull()
                    ->blocks([
                        Block::make('h2')
                            ->schema([
                                TextInput::make('value')->required(),
                                Toggle::make('animate')->label('Animate'),
                                TextInput::make('delay')->default('0.3'),
                            ]),
                         Block::make('p')
                             ->schema([
                                 Textarea::make('value')
                                     ->label('Paragraph')
                                     ->required(),
                                 Toggle::make('animate')->label('Animate'),
                                 TextInput::make('delay')->default('0.4'),
                             ]),
                        Block::make('list')
                        ->label('list')
                        ->schema([
                            Repeater::make('items')
                            ->label('items')
                                ->schema([
                                    TextInput::make('text')
                                        ->label('Text')
                                        ->required(),
                                    TextInput::make('delay')
                                        ->label('Delay')
                                        ->placeholder('0.1')
                                        ->default('0.1'),
                                    Toggle::make('animate')
                                        ->label('Animate')
                                        ->default(true),
                                ])
                                ->defaultItems(3)
                                ->reorderable()
                                ->collapsed(),
                        ])
                    ]),
            ]);
    }
}
