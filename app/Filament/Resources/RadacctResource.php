<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RadacctResource\Pages;
use App\Filament\Resources\RadacctResource\RelationManagers;
use App\Models\Radacct;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Actions;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RadacctResource extends Resource
{
    protected static ?string $model = Radacct::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-rectangle-stack';
    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
      
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                 Tables\Columns\TextColumn::make('nasipaddress')->label('Public ip ')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('acctstarttime')->label('START TIME')->searchable(),
            Tables\Columns\TextColumn::make('acctstoptime')->label('Stop Time')->searchable(),
            Tables\Columns\TextColumn::make('acctsessiontime')->label('Duration')
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
    }),
            Tables\Columns\TextColumn::make('acctinputoctets')->label('upload')
            ->formatStateUsing(fn ($state) => number_format($state / 1_073_741_824, 2) . ' GB')
,
            Tables\Columns\TextColumn::make('acctoutputoctets')->label('Download')
            ->formatStateUsing(fn ($state) => number_format($state / 1_073_741_824, 2) . ' GB')
,
            Tables\Columns\TextColumn::make('acctterminatecause')->label('Drop cause')->sortable(),
            Tables\Columns\TextColumn::make('framedipaddress')->label('local ip address'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
              //  Actions\EditAction::make(),
            ])
            ->toolbarActions([
                // Actions\BulkActionGroup::make([
                //     Actions\DeleteBulkAction::make(),
                // ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRadaccts::route('/'),
            // 'create' => Pages\CreateRadacct::route('/create'),
            // 'edit' => Pages\EditRadacct::route('/{record}/edit'),
        ];
    }
}
