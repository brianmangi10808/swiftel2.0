<?php

namespace App\Filament\Resources\Devices\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use App\Models\Device;
use Filament\Actions\Action;

class DevicesTable
{
    public static function configure(Table $table): Table
    {
        return $table

            ->heading('Devices Available')
        ->description('Create Devices Packages To Be Displayed On Your Portal')
        ->paginated([10, 25, 50, 100])
        ->searchable()
        ->striped()
        ->emptyStateHeading('No Devices  Created')
        ->emptyStateDescription('Once you Create Device, it will appear here.')
        ->emptyStateActions([
            Action::make('create')
                ->label('Create Devices ')
                ->icon('heroicon-m-plus')
                ->button(),
        ])
        ->recordClasses(fn (Device $record) => match ($record->is_active) {
            'true' => 'draft-post-table-row',
            default => null,
        })
            ->columns([
                TextColumn::make('shortname')
                    ->searchable(),
                TextColumn::make('nasname')
                    ->searchable()
                     ->label('ip address')
                     ->placeholder('e.g., 102.0.24.21'),
                TextColumn::make('api_username')
                    ->searchable()
                    ->label('api username')
                    ->placeholder('e.g., admin'),
                
                TextColumn::make('api_port')
             
                   ->label('API Port')
                    ->default(8728),
                TextColumn::make('location')
                    ->searchable()
                     ->label('Location')
                     ->placeholder('e.g Data Center'),
                TextColumn::make('status')
                    ->badge()
                    ->colors([
                    'success' => 'online',
                    'danger'  => 'offline',
                ]),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('company_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('server')
                    ->searchable(),
                TextColumn::make('type')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
