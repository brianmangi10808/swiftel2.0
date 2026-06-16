<?php

namespace App\Filament\Resources\Packages\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class PackagesForm
{
    public static function configure(Schema $schema): Schema
    {
        $user = Auth::user();

        return $schema
            ->columns(2)
            ->components([
                Select::make('company_id')
                    ->label('Company')
                    ->relationship('company', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->visible(fn (): bool => (bool) $user?->is_super_admin)
                    ->default(fn () => $user?->company_id)
                    ->columnSpanFull(),

                      Section::make('Package Name')
                    ->description('Create a  package to be displayed on your portal')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([

                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('e.g. "2 Hours Package"')
                    ->columnSpanFull(),

                TextInput::make('duration_sec')
                    ->label('Duration')
                    ->required()
                    ->numeric()
                    ->suffix('seconds')
                    ->placeholder('e.g. 7200'),

                TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('KES')
                    ->placeholder('e.g. 20'),

                      ]),

                Section::make('Speed Limits')
                    ->description('Set the bandwidth caps for this package.')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('speed_down')
                            ->label('Download Speed')
                            ->required()
                            ->maxLength(255)
                            ->suffix('Mbps')
                            ->placeholder('e.g. 10M'),

                        TextInput::make('speed_up')
                            ->label('Upload Speed')
                            ->required()
                            ->maxLength(255)
                            ->suffix('Mbps')
                            ->placeholder('e.g. 10M'),
                    ]),

                Toggle::make('is_active')
                    ->label('Active')
                    ->default(true)
                    ->columnSpanFull(),
            ]);
    }
}