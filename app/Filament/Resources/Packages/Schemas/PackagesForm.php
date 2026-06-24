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

                Select::make('duration_sec')
    ->label('Duration')
    ->required()
    ->native(false)
    ->searchable()
    ->options([
        180     => '3 min',
        900     => '15 min',
        1800    => '30 min',
        2700    => '45 min',
        3600    => '1 hour',
        5400    => '1 h 30 min',
        7200    => '2 hrs',
        9000    => '2 hrs 30 min',
        10800   => '3 hrs',
        12600   => '3 hrs 30 min',
        14400   => '4 hrs',
        25200   => '7 hrs',
        32400   => '9 hrs',
        43200   => '12 hrs',
        64800   => '18 hrs',
        72000   => '20 hrs',
        172800  => '2 days',
        259200  => '3 days',
        345600  => '4 days',
        604800  => '7 days',
        864000  => '10 days',
        1209600 => '14 days',
        2592000 => '30 days',
        5184000 => '60 days',
    ]),
                
                

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