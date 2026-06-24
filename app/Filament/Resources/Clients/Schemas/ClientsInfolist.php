<?php

namespace App\Filament\Resources\Clients\Schemas;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ClientsInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                 Section::make()
         
                   ->columnSpanFull()
                   ->columns([
                        'default' => 2,  
                        'sm' => 2,
                        'lg' =>4,       
                          ])
                    ->schema([
                        TextEntry::make('phone')
                         ->tooltip('customers username')
                         ->copyable()
                         ->copyMessage('Copied!')
                         ->copyMessageDuration(1500)
                            ->label('Username')
                            ->weight('bold'),

                        TextEntry::make('password')
                        ->copyable()
                         ->copyMessage('Copied!')
                         ->copyMessageDuration(1500)
                            ->label('Password'),

                        TextEntry::make('package.name')
                            
                            ->icon('heroicon-o-wifi'),

                        TextEntry::make('router')
                            ->label('Router'),

                        TextEntry::make('payment_status')
                            ->label('payment_status')
                            ->icon('heroicon-o-wallet'),

                        

                        TextEntry::make('expiring_date')
                            ->dateTime('M j, Y H:i'),

                        TextEntry::make('mpesa_receipt')
                            
                    ]),

            ]);
    }
}
