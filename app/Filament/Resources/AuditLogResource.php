<?php

namespace App\Filament\Resources;

use Filament\Resources\Resource;
use App\Filament\Resources\AuditLogResource\Pages;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Forms;
use Filament\Forms\Form;
use OwenIt\Auditing\Models\Audit;

class AuditLogResource extends Resource
{
    protected static ?string $model = Audit::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationGroup = 'System Logs';
        protected static ?int $navigationSort = 51;

    protected static ?string $navigationLabel = 'Audit Logs';
    

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),

                TextColumn::make('user_type')
                    ->label('User Model')
                    ->limit(20),

                TextColumn::make('user_id')
                    ->label('User ID'),

                TextColumn::make('event')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'created' => 'success',
                        'updated' => 'warning',
                        'deleted' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('auditable_type')
                    ->label('Model')
                    ->limit(25),

                TextColumn::make('auditable_id')
                    ->label('Model ID'),

                TextColumn::make('ip_address')
                    ->label('IP Address'),

                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime()
                    ->sortable(),
            ])

            ->filters([
                SelectFilter::make('event')
                    ->options([
                        'created' => 'Created',
                        'updated' => 'Updated',
                        'deleted' => 'Deleted',
                    ]),

                Filter::make('date_range')
                    ->form([
                        Forms\Components\DatePicker::make('from'),
                        Forms\Components\DatePicker::make('to'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['from'], fn ($q) =>
                                $q->whereDate('created_at', '>=', $data['from'])
                            )
                            ->when($data['to'], fn ($q) =>
                                $q->whereDate('created_at', '<=', $data['to'])
                            );
                    }),
            ])

            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
{
    return [
        'index' => Pages\ListAuditLogs::route('/'),
    ];
}

}
