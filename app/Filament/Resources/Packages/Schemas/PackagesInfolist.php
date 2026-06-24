<?php

namespace App\Filament\Resources\Packages\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class PackagesInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
         

                   ->columns([
                        'default' => 2,  
                        'sm' => 2,
                        'lg' => 3,       
                          ])
                    ->schema([
                        TextEntry::make('name')
                            ->label('Package Name')
                            ->weight('bold'),

                       TextEntry::make('duration_sec')
    ->label('Duration')
    ->formatStateUsing(function (int $state): string {
        if ($state < 3600) {
            $min = round($state / 60);
            return $min . ' min';
        }

        if ($state < 86400) {
            $hrs = $state / 3600;
            return rtrim(rtrim(number_format($hrs, 1), '0'), '.') . ' ' . ($hrs == 1 ? 'hour' : 'hrs');
        }

        $days = $state / 86400;
        return rtrim(rtrim(number_format($days, 1), '0'), '.') . ' ' . ($days == 1 ? 'day' : 'days');
    })
    ->icon('heroicon-o-clock'),

                        TextEntry::make('price')
                            ->money('KES')
                            ->icon('heroicon-o-banknotes'),

                        TextEntry::make('speed_down')
                            ->label('Download Speed')
                            ->suffix(' Mbps')
                            ->icon('heroicon-o-arrow-down-tray'),

                        TextEntry::make('speed_up')
                            ->label('Upload Speed')
                            ->suffix(' Mbps')
                            ->icon('heroicon-o-arrow-up-tray'),

                        IconEntry::make('is_active')
                            ->label('Status')
                            ->boolean(),

                        TextEntry::make('created_at')
                            ->dateTime('M j, Y H:i'),

                        TextEntry::make('updated_at')
                            ->dateTime('M j, Y H:i'),
                    ]),

                  
                Section::make('Clients')
                    ->description('Clients who have purchased this hotspot package')
                    ->icon(Heroicon::User)
                    ->collapsible()
                    ->schema([
                        RepeatableEntry::make('clients')
                           ->getStateUsing(fn ($record) => $record->clients()->where('payment_status', 'credited')->get())
                            ->hiddenLabel()
                            ->columns([
                                'default' => 2,
                                'lg' => 4,
                            ])
                            ->schema([
                                TextEntry::make('phone')
                                    ->icon('heroicon-o-phone'),

                                TextEntry::make('ip')
                                    ->label('IP Address'),

                                TextEntry::make('payment_status')
                                    ->badge()
                                    ->color(fn (?string $state): string => match ($state) {
                                        'credited' => 'success',
                                        'pending'  => 'warning',
                                        default    => 'gray',
                                    }),

                                TextEntry::make('expiring_date')
                                    ->label('Expires')
                                    ->dateTime('M j, Y H:i'),
                            ]),
                    ]),
            ]);
    }
}