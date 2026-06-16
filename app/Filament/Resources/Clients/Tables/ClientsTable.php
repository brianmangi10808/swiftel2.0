<?php

namespace App\Filament\Resources\Clients\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\ColumnGroup;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Auth;






class ClientsTable
{
    public static function configure(Table $table): Table
    {
        return $table

           ->heading('Hotspot Customers')
        ->description('Customers who buy your Hotspot packages will be displayed Here ')
        ->paginated([10, 25, 50, 100])
        ->searchable()
        ->striped()
        ->emptyStateHeading('No Hotspot Customers Exist')
        ->emptyStateDescription('Once Customers Buy  Hotspot Package ,They  will appear here.')
        ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('payment_status', ['credited', 'uncredited']))
        ->emptyStateActions([
            Action::make('create')
                ->label('Create Hotspot Customers')
                ->icon('heroicon-m-plus')
                ->button(),
        ])
        
            ->columns([
                // 
                 ColumnGroup::make('Customers Details', [
                TextColumn::make('phone')
             ->searchable()
              ->label('Username/Phone')
                  ->toggleable(),
               TextColumn::make('package.name')
               ->label('Package ')
               ->searchable()
               ->toggleable(),
               TextColumn::make('password')
               ->toggleable(),
                 ]),
               TextColumn::make('router')
               ->toggleable(),
               ColumnGroup::make('Finance', [
              
               TextColumn::make('payment_status')
               ->toggleable(),
               TextColumn::make('mpesa_receipt')
               ->toggleable()
              ]),
              TextColumn::make('expiring_date')
              ->toggleable()
                  ->isoDateTime()
,
                // 
            ])
            ->filters([
                //
                SelectFilter::make('package_id')
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
        ->preload(),
        SelectFilter::make('expiry_status')
    ->label('Expiry status')
    ->options([
        'expired' => 'Expired',
        'soon'    => 'Expiring soon (7 days)',
        'active'  => 'Active',
    ])
    ->query(function (Builder $query, array $data): Builder {
        return match ($data['value'] ?? null) {
            'expired' => $query->whereDate('expiring_date', '<', now()),
            'soon'    => $query->whereBetween('expiring_date', [now(), now()->addDays(7)]),
            'active'  => $query->whereDate('expiring_date', '>=', now()),
            default   => $query,
        };
    }),

            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
