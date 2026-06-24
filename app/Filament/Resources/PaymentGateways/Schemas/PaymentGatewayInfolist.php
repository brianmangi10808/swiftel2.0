<?php

namespace App\Filament\Resources\PaymentGateways\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class PaymentGatewayInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Gateway Information')
                    ->schema([
                        TextEntry::make('name')
                            ->label('Gateway Name'),
                        
                        TextEntry::make('gateway_type')
                            ->label('Type')
                            ->badge()
                            ->formatStateUsing(fn ($state) => match($state) {
                                'mpesa' => 'M-Pesa',
                                'payhero' => 'PayHero',
                                'bank' => 'Bank Transfer',
                                default => ucfirst($state),
                            })
                            ->color(fn ($state) => match($state) {
                                'mpesa' => 'success',
                                'payhero' => 'info',
                                'bank' => 'primary',
                                default => 'gray',
                            }),
                        
                        IconEntry::make('is_active')
                            ->label('Active')
                            ->boolean(),
                        
                        IconEntry::make('is_default')
                            ->label('Default')
                            ->boolean(),
                        
                        TextEntry::make('company.name')
                            ->label('Company'),
                        
                        TextEntry::make('created_at')
                            ->label('Created At')
                            ->dateTime(),
                    ])->columns(2),
                
                // M-Pesa Configuration
                Section::make('M-Pesa Configuration')
                    ->schema([
                        TextEntry::make('config.short_code')
                            ->label('Business Shortcode')
                            ->copyable(),
                        
                        TextEntry::make('config.passkey')
                            ->label('API Passkey')
                            ->copyable(),
                        
                        TextEntry::make('config.consumer_key')
                            ->label('Consumer Key')
                            ->copyable(),
                        
                        TextEntry::make('config.consumer_secret')
                            ->label('Consumer Secret')
                            ->copyable(),
                    ])
                    ->visible(fn ($record) => $record->gateway_type === 'mpesa')
                    ->columns(2),
                
                // PayHero Configuration
                Section::make('PayHero Configuration')
                    ->schema([
                        TextEntry::make('config.basic_auth')
                            ->label('Basic Auth')
                            ->copyable(),
                        
                        TextEntry::make('config.api_username')
                            ->label('API Username')
                            ->copyable(),
                        
                        TextEntry::make('config.account_id')
                            ->label('Account ID')
                            ->copyable(),
                        
                        TextEntry::make('config.api_password')
                            ->label('API Password')
                            ->copyable(),
                    ])
                    ->visible(fn ($record) => $record->gateway_type === 'payhero')
                    ->columns(2),
                
                // 🆕 Bank Configuration
                Section::make('Bank Configuration')
                    ->schema([
                        TextEntry::make('config.bank_name')
                            ->label('Bank Name')
                            ->copyable()
                            ->icon('heroicon-o-building-library'),
                        
                        TextEntry::make('config.bank_paybill')
                            ->label('Paybill Number')
                            ->copyable()
                            ->icon('heroicon-o-credit-card'),
                        
                        TextEntry::make('config.bank_account_number')
                            ->label('Account Number')
                            ->copyable()
                            ->icon('heroicon-o-document-text'),
                        
                      
                    ])
                    ->visible(fn ($record) => $record->gateway_type === 'bank')
                    ->columns(2),
            ]);
    }
}