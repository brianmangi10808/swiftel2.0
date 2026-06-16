<?php

namespace App\Filament\Resources\Clients\Schemas;
use Illuminate\Support\Facades\Auth;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Filament\Support\Icons\Heroicon;
use Filament\Support\Enums\IconPosition;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Tabs;
use Filament\Forms\Components\DateTimePicker;




class ClientsForm
{
    public static function configure(Schema $schema): Schema
    {
                $user = Auth::user();

        return $schema

        
            ->components([
                
                Tabs::make('Tabs')
    ->tabs([
        Tab::make('Notifications')
            ->icon(Heroicon::Bell)
            ->iconPosition(IconPosition::After)
            ->schema([
                // ...
            ]),
        // ...
    ]),
                Select::make('company_id')
                    ->label('Company')
                    ->relationship('company', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->visible(fn (): bool => (bool) $user?->is_super_admin)
                    ->default(fn () => $user?->company_id)
                    ->columnSpanFull(),
                //
                 Section::make('Customers Details')
                   ->columns([
                        'default' => 2,
                        'sm' => 2,
                        'lg' => 3,
                    ])
                    ->description('Create a  Customer to be authenticated ')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([

                TextInput::make('phone')
                    ->required()
                    ->label('username & Phone')
                    ->maxLength(255)
                    ->columnSpanFull(),
                  
                      TextInput::make('password')
                    ->required()
                    ->label('password')
                    ->maxLength(255),

                    
                        

  ]),
                     Section::make('Customers Packages')
                       ->columns([
                        'default' => 2,
                        'sm' => 2,
                        'lg' => 3,
                    ])
                    ->description('Select a  Customer plan ')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([

                    Select::make('package_id')
                    ->label('Package')
                    ->relationship(
                    'package',
                    'name',
                     fn (Builder $query) => $query->when(
                    ! Auth::user()?->is_super_admin,
                      fn (Builder $q) => $q->where('company_id', Auth::user()->company_id),
                    ),
                   )
                   ->searchable()
                   ->preload()
                   ->required()
                  ->helperText('Create a package if none exist.'),

                     TextInput::make('payment_status')
    ->default('uncredited')
    ->disabled()         
    ->dehydrated()       
    ->helperText('Set automatically. Becomes "credited" once payment is confirmed.'),

                DateTimePicker::make('expiring_date')
                    ->required()
                   
,

                      ]),

            ]);
    }
}
