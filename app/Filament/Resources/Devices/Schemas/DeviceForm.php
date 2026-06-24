<?php

namespace App\Filament\Resources\Devices\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;

class DeviceForm
{
    public static function configure(Schema $schema): Schema
    {
        $user = Auth::user();

        return $schema
            ->components([
                Wizard::make([
                    Step::make('Order')
                        ->schema([
                            Select::make('company_id')
                                ->label('Company')
                                ->relationship('company', 'name')
                                ->searchable()
                                ->preload()
                                ->required()
                                ->visible(fn (): bool => (bool) $user?->is_super_admin)
                                ->default(fn () => $user?->company_id)
                                ->columnSpanFull(),

                            Section::make('Device Details')
                                ->description('Create a Device')
                                ->columns(2)
                                ->columnSpanFull()
                                ->schema([
                                    TextInput::make('shortname')->required(),
                                    TextInput::make('nasname')->required(),
                                    TextInput::make('api_username')->required(),
                                    TextInput::make('secret')->required(),
                                ]),
                        ]),

                    Step::make('Location')
                        ->schema([
                            Section::make('Device Location')
                                ->description('Where is device located')
                                ->columns(2)
                                ->columnSpanFull()
                                ->schema([
                                    TextInput::make('api_port')->default(8728),
                                    TextInput::make('location'),
                                   
                                    TextInput::make('server'),
                                    TextInput::make('type')
                                        ->label('Type of Router')
                                        ->placeholder('e.g CCR2116'),
                                ]),
                        ]),
                ])
                ->columnSpanFull(),
            ]);
    }
}