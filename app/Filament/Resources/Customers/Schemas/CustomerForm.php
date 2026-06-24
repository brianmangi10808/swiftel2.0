<?php

namespace App\Filament\Resources\Customers\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Tabs;
use Illuminate\Support\Facades\Auth;

class CustomerForm
{
    public static function configure(Schema $schema): Schema
    {

     $user = Auth::user();

        return $schema
            ->components([
                  Tabs::make('Tabs'),
                TextInput::make('company_id')
                    ->numeric(),
                TextInput::make('username')
                    ->required(),
                TextInput::make('password')
                    ->password()
                    ->required(),
                Select::make('status')
                    ->options(['online' => 'Online', 'offline' => 'Offline', 'expired' => 'Expired'])
                    ->default('offline'),
                Toggle::make('enable')
                    ->required(),
                TextInput::make('sector_id')
                    ->numeric(),
                TextInput::make('premise_id')
                    ->numeric(),
                TextInput::make('service_id')
                    ->numeric(),
                Toggle::make('allow_mac')
                    ->required(),
                TextInput::make('simultaneous_use')
                    ->required()
                    ->numeric()
                    ->default(1),
                TextInput::make('Calling_Station_Id'),
                TextInput::make('group_id')
                    ->numeric(),
                TextInput::make('credit')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('firstname'),
                TextInput::make('lastname'),
                TextInput::make('mobile_number'),
                TextInput::make('email')
                    ->label('Email address')
                    ->email(),
                DateTimePicker::make('expiry_date'),
                Textarea::make('comment')
                    ->columnSpanFull(),
                Textarea::make('attribute')
                    ->columnSpanFull(),
            ]);
    }
}
