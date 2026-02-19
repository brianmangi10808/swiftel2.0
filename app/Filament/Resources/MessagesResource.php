<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MessagesResource\Pages;
use App\Filament\Resources\MessagesResource\RelationManagers;
use App\Models\Messages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Model;

class MessagesResource extends Resource
{
    protected static ?string $model = Messages::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
     protected static ?string $navigationGroup = 'Communication';
         protected static ?int $navigationSort = 22;

                           public static function canViewAny(): bool
{
    return Auth::user()?->can('read messages') ?? false;
}

public static function canView(Model $record): bool
{
    return Auth::user()?->can('read messages') ?? false;
}

public static function canCreate(): bool
{
    return Auth::user()?->can('create messages') ?? false;
}

public static function canEdit(Model $record): bool
{
    return Auth::user()?->can('update messages') ?? false;
}

public static function canDelete(Model $record): bool
{
    return Auth::user()?->can('delete messages') ?? false;
}

public static function canDeleteAny(): bool
{
    return Auth::user()?->can('delete messages') ?? false;
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
                //
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
          //      TextColumn::make("id"),
                TextColumn::make("recipient"),
                TextColumn::make("message_body"),
               // TextColumn::make("channel"),
                TextColumn::make("created_at")
            ])
             ->defaultSort('id', 'desc')
            ->filters([
                //
            ])
            ->actions([
               // Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
               // Tables\Actions\BulkActionGroup::make([
                   // Tables\Actions\DeleteBulkAction::make(),
              //  ]),
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
            'index' => Pages\ListMessages::route('/'),
            //'create' => Pages\CreateMessages::route('/create'),
           // 'edit' => Pages\EditMessages::route('/{record}/edit'),
        ];
    }
}
