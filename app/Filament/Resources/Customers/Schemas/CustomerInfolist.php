<?php

namespace App\Filament\Resources\Customers\Schemas;

use App\Models\Customer;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;

use Filament\Schemas\Schema;

class CustomerInfolist
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
                        'lg' =>6,       
                          ])
                 ->schema([
                
                TextEntry::make('username'),
                TextEntry::make('status')
                    ->badge()
                    ->placeholder('-')
                    ->colors([
        'danger' => fn ($state) => strtolower($state) === 'offline',
        'warning' => fn ($state) => strtolower($state) === 'expired',
        'success' => fn ($state) => strtolower($state) === 'online',
    ]),
                IconEntry::make('enable')
                    ->boolean(),
                TextEntry::make('sector.name')
                    ->label('Sector')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('premise.name')
                    ->label('Premise')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('service.name')
                    ->label('Service')
                    ->numeric()
                    ->placeholder('-'),
                IconEntry::make('allow_mac')
                    ->boolean(),
                TextEntry::make('simultaneous_use')
                    ->numeric(),
                TextEntry::make('Calling_Station_Id')
                    ->placeholder('-'),
                TextEntry::make('group.name')
                    ->label('Group')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('credit')
                    ->numeric(),
                TextEntry::make('firstname')
                    ->placeholder('-'),
                TextEntry::make('lastname')
                    ->placeholder('-'),
                TextEntry::make('mobile_number')
                    ->placeholder('-'),
                TextEntry::make('email')
                    ->label('Email address')
                    ->placeholder('-'),
                TextEntry::make('expiry_date')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('comment')
                    ->placeholder('-'),
                    
                TextEntry::make('attribute')
                    ->placeholder('-'),
                   
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('deleted_at')
                    ->dateTime()
                    ->visible(fn (Customer $record): bool => $record->trashed())
            

                         
                    ]),

            ]);
    }
}