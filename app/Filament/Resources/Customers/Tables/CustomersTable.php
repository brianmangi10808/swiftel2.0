<?php

namespace App\Filament\Resources\Customers\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Table;

class CustomersTable
{
    public static function configure(Table $table): Table
    {
        return $table
          ->heading('PPPOE Customers')
            ->description('Create PPPOE Customers With Unique details')
             ->paginated([ 25, 50, 100])
             ->striped()
              ->emptyStateHeading('No PPPOE Customers Created')
              ->emptyStateDescription('Once you Create Customers , They will appear here.')
            ->columns([
                TextColumn::make('company_id')
                    ->numeric()
                    ->sortable()
                     ->visible(fn () => Auth::user()?->is_super_admin),
                TextColumn::make('username')
                    ->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->colors([
        'danger' => fn ($state) => strtolower($state) === 'offline',
        'warning' => fn ($state) => strtolower($state) === 'expired',
        'success' => fn ($state) => strtolower($state) === 'online',
    ]),
                IconColumn::make('enable')
                    ->boolean()
                     ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('sector.name')
                    ->numeric()
                    ->sortable()
                    ->label('Sector')
                     ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('premise.name')
                    ->numeric()
                    ->sortable()
                    ->label('Premise')
                     ->toggleable(isToggledHiddenByDefault: true),
        TextColumn::make('service.name')
    ->label('Service'),
                IconColumn::make('allow_mac')
                    ->boolean()
                     ->toggleable(isToggledHiddenByDefault: true),
              
                
                TextColumn::make('group.name')
                    ->label('Group')
                    ->numeric()
                    ->sortable()
                     ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('credit')
                    ->numeric()
                    ->sortable()
                     ->toggleable(isToggledHiddenByDefault: true),

TextColumn::make('full_name')
    ->label('Name')
    ->getStateUsing(fn ($record) => trim("{$record->firstname} {$record->lastname}"))
    ->sortable(['firstname', 'lastname'])
    ->searchable(['firstname', 'lastname']),

             
                TextColumn::make('mobile_number')
                    ->searchable()
                     ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('email')
                    ->label('Email address')
                    ->searchable()
                     ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('expiry_date')
                    ->dateTime()
                    ->sortable()
                     ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
