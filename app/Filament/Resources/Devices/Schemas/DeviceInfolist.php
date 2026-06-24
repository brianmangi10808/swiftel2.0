<?php

namespace App\Filament\Resources\Device\Schemas;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DeviceInfolist
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
                        TextEntry::make('shortname')
                         ->copyable()
                         ->copyMessage('Copied!')
                         ->copyMessageDuration(1500)
                            ->label('Device name')
                            ->weight('bold'),

                        TextEntry::make('nasname')
                        ->copyable()
                         ->copyMessage('Copied!')
                         ->copyMessageDuration(1500)
                            ->label('Ip Address'),

                        TextEntry::make('api_username')
                            
                            ->label('Api Username'),

                        TextEntry::make('secret')
                            ->label('Secret & Password'),

                        TextEntry::make('api_port')
                            ->label('Api Port')
                            ->default(8728),

                        

                        TextEntry::make('location')
                            ->label('Location'),

                        TextEntry::make('status')
                         ->badge()
                    ->colors([
                    'success' => 'online',
                    'danger'  => 'offline',
                ]),
                            
                    ]),

            ]);
    }
}
