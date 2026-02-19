<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SectorResource\Pages;
use App\Filament\Resources\SectorResource\RelationManagers;
use App\Models\Sector;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Model;

class SectorResource extends Resource
{
    protected static ?string $model = Sector::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
      protected static ?string $navigationGroup = 'Area';
          protected static ?int $navigationSort = 31;

               public static function canViewAny(): bool
{
    return Auth::user()?->can('read sectors') ?? false;
}

public static function canView(Model $record): bool
{
    return Auth::user()?->can('read sectors') ?? false;
}

public static function canCreate(): bool
{
    return Auth::user()?->can('create sectors') ?? false;
}

public static function canEdit(Model $record): bool
{
    return Auth::user()?->can('update sectors') ?? false;
}

public static function canDelete(Model $record): bool
{
    return Auth::user()?->can('delete sectors') ?? false;
}

public static function canDeleteAny(): bool
{
    return Auth::user()?->can('delete sectors') ?? false;
}
public static function getEloquentQuery(): Builder
{
    $query = parent::getEloquentQuery();
    $user = Auth::user();

    // ✅ Super Admin → sees all tickets
    if ($user?->is_super_admin) {
        return $query;
    }

    // ✅ Company users → only their company tickets
    return $query->where('company_id', $user->company_id);
}

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                                Forms\Components\Hidden::make('company_id')
    ->default(fn () => Auth::user()?->company_id),
                  Forms\Components\TextInput::make('name')
                ->required()
                ->label('Sector Name')
                ->maxLength(254),

            Forms\Components\Textarea::make('description')
                ->rows(2)
                ->maxLength(254)
                ->label('Description'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                          Tables\Columns\TextColumn::make('company.name')
    ->label('Company')
    ->sortable()
    ->toggleable()
    ->visible(fn () => \Illuminate\Support\Facades\Auth::user()?->is_super_admin),
                 Tables\Columns\TextColumn::make('name')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('description')->limit(40),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->label('Created'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListSectors::route('/'),
            'create' => Pages\CreateSector::route('/create'),
            'edit' => Pages\EditSector::route('/{record}/edit'),
        ];
    }
}
