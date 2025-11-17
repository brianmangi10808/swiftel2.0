<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RadacctResource\Pages;
use App\Filament\Resources\RadacctResource\RelationManagers;
use App\Models\Radacct;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RadacctResource extends Resource
{
    protected static ?string $model = Radacct::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
      
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                 Tables\Columns\TextColumn::make('nasipaddress')->label('ip address')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('acctstarttime')->label('START TIME')->searchable(),
            Tables\Columns\TextColumn::make('acctstoptime')->label('Stop Time')->searchable(),
            Tables\Columns\TextColumn::make('acctsessiontime')->label('Duration'),
            Tables\Columns\TextColumn::make('acctinputoctets')->label('acctinputoctets'),
            Tables\Columns\TextColumn::make('acctoutputoctets')->label('acctoutputoctets'),
            Tables\Columns\TextColumn::make('acctterminatecause')->label('acctterminatecause')->sortable(),
            Tables\Columns\TextColumn::make('framedipaddress')->label('framedipaddress'),
            ])
            ->filters([
                //
            ])
            ->actions([
              //  Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
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
