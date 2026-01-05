<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GroupResource\Pages;
use App\Filament\Resources\GroupResource\RelationManagers;
use App\Models\Group;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class GroupResource extends Resource
{
    protected static ?string $model = Group::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
       protected static ?string $navigationGroup = 'Area';
           protected static ?int $navigationSort = 30;

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
                ->label('Group Name')
                ->required()
                ->maxLength(254),

            Forms\Components\Textarea::make('description')
                ->label('Description')
                ->rows(2)
                ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                                            TextColumn::make('company.name')
    ->label('Company')
    ->sortable()
    ->toggleable()
    ->visible(fn () => \Illuminate\Support\Facades\Auth::user()?->is_super_admin),
                 Tables\Columns\TextColumn::make('name')->sortable()->searchable(),
            Tables\Columns\TextColumn::make('description')->limit(40),
            Tables\Columns\TextColumn::make('created_at')->dateTime()->label('Created'),
            ])
            ->defaultSort('id', 'desc')
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
            'index' => Pages\ListGroups::route('/'),
            'create' => Pages\CreateGroup::route('/create'),
            'edit' => Pages\EditGroup::route('/{record}/edit'),
        ];
    }
}
