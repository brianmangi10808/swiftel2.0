<?php

namespace App\Filament\Resources\PaymentGateways\Schemas;

use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class PaymentGatewayForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('company_id')
                    ->default(fn () => auth()->user()->company_id),
                
                Section::make('Gateway Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('e.g., "ABSA Bank"')
                                    ->helperText('A descriptive name for this gateway')
                                    ->columnSpan(1),
                                
                                Select::make('gateway_type')
                                    ->required()
                                    ->live()
                                    ->disabled(fn ($operation) => $operation === 'edit')
                                    ->options([
                                        'mpesa' => 'M-Pesa (Safaricom)',
                                        'payhero' => 'PayHero',
                                        'bank' => 'Bank Transfer',
                                    ])
                                    ->helperText(fn ($operation) => $operation === 'edit' ? 'Gateway type cannot be changed' : 'Select the payment provider')
                                    ->columnSpan(1),
                                
                                Toggle::make('is_active')
                                    ->label('Active')
                                    ->default(true)
                                    ->columnSpan(1),
                                
                                Toggle::make('is_default')
                                    ->label('Set as Default')
                                    ->helperText('Customers will see this as the primary payment method')
                                    ->columnSpan(1),
                            ]),
                    ]),
                
                // M-Pesa Configuration
                Section::make('M-Pesa Configuration')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('short_code')
                                    ->label('Business Shortcode')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('e.g., 174379'),
                                
                                TextInput::make('passkey')
                                    ->label('API Passkey')
                                    ->required()
                                    ->maxLength(255),
                                
                                TextInput::make('consumer_key')
                                    ->label('Consumer Key')
                                    ->required()
                                    ->maxLength(255),
                                
                                TextInput::make('consumer_secret')
                                    ->label('Consumer Secret')
                                    ->required()
                                    ->maxLength(255),
                            ]),
                    ])
                    ->visible(fn ($get, $operation, $record) => 
                        $operation === 'create' 
                            ? $get('gateway_type') === 'mpesa'
                            : ($record && $record->gateway_type === 'mpesa')
                    ),
                
                // PayHero Configuration
                Section::make('PayHero Configuration')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('basic_auth')
                                    ->label('Basic Auth')
                                    ->required()
                                    ->maxLength(255),
                                
                                TextInput::make('api_username')
                                    ->label('API Username')
                                    ->maxLength(255),
                                
                                TextInput::make('account_id')
                                    ->label('Account ID')
                                    ->maxLength(255),
                                
                                TextInput::make('api_password')
                                    ->label('API Password')
                                    ->maxLength(255),
                            ]),
                    ])
                    ->visible(fn ($get, $operation, $record) => 
                        $operation === 'create' 
                            ? $get('gateway_type') === 'payhero'
                            : ($record && $record->gateway_type === 'payhero')
                    ),
                
                // Bank Configuration
                Section::make('Bank Configuration')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('bank_name')
                                    ->label('Bank Name')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('e.g., ABSA BANK')
                                    ->helperText('Name of the bank'),
                                
                                TextInput::make('bank_paybill')
                                    ->label('Bank Paybill Number')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('e.g., 880100')
                                    ->helperText('The paybill number for this bank'),
                                
                                TextInput::make('bank_account_number')
                                    ->label('Bank Account Number')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('e.g., 9793890026')
                                    ->helperText('The account number to receive payments'),
                                
                           
                                TextInput::make('channel_id')
                                    ->label('PayHero Channel ID')
                                    ->disabled()
                                    ->placeholder('Auto-filled after API registration')
                                    ->helperText('Will be automatically filled when you save'),
                            ]),
                    ])
                    ->visible(fn ($get, $operation, $record) => 
                        $operation === 'create' 
                            ? $get('gateway_type') === 'bank'
                            : ($record && $record->gateway_type === 'bank')
                    ),
                
                // Placeholder when no gateway selected
                Placeholder::make('select_gateway')
                    ->content('⚠️ Please select a gateway type from the dropdown above to configure its settings.')
                    ->columnSpanFull()
                    ->visible(fn ($get, $operation) => $operation === 'create' && empty($get('gateway_type'))),
            ]);
    }
}