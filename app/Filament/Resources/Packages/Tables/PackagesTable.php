<?php

namespace App\Filament\Resources\Packages\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Table;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Filament\Tables\Columns\ColumnGroup;
use App\Models\Packages;
use Filament\Actions\Action;
use Filament\Support\Enums\FontWeight;
use Closure;

class PackagesTable
{

    public static function configure(Table $table): Table
    {
        return $table
        ->heading('Hotspots Packages')
        ->description('Create Hotspot Packages To Be Displayed On Your Portal')
        ->paginated([10, 25, 50, 100])
        ->searchable()
        ->striped()
        ->reorderable('sort')
        ->emptyStateHeading('No Hotspot Packages Created')
        ->emptyStateDescription('Once you Create Hotspot Package , it will appear here.')
        ->emptyStateActions([
            Action::make('create')
                ->label('Create Hotspot Packages')
                ->icon('heroicon-m-plus')
                ->button(),
        ])
        ->recordClasses(fn (Packages $record) => match ($record->is_active) {
            'true' => 'draft-post-table-row',
            default => null,
        })
            ->columns([
            TextColumn::make('name')
             ->searchable()
             ->placeholder('12 Hours Plan')
             ->label('Package Name')
             ->weight(FontWeight::Bold),
            
            TextColumn::make('duration_sec')
            ->label('Duration'),
            
            ColumnGroup::make('Visibility', [
            TextColumn::make('speed_down')
            ->label('Download Speeds'),
           
            TextColumn::make('speed_up')
            ->label('Upload Speeds'),
            
            ]),
            TextColumn::make('price')
             ->sortable()
             ->label('Package Price')
             ->money('KSH', decimalPlaces: 0)
             ->toggleable(),
            IconColumn::make('is_active')
                ->boolean()
                ->toggleable(),
            ])
            ->filters([
                  Filter::make('is_active')
                ->query(fn (Builder $query) => $query->where('is_active', true)),
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
