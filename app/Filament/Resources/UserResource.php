<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Models\Permission;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
  //protected static ?string $navigationGroup = 'Network Devices';
  
    protected static ?string $navigationIcon = 'heroicon-o-users';
    
public static function getEloquentQuery(): Builder
{
    $query = parent::getEloquentQuery();
    $user = Auth::user();

    
    if ($user?->is_super_admin) {
        return $query;
    }

    return $query->where('company_id', $user->company_id);
}


  public static function canViewAny(): bool
    {
        return Auth::user()?->can('read users') ?? false;
    }

    public static function canView(Model $record): bool
    {
        return Auth::user()?->can('read users') ?? false;
    }

    public static function canCreate(): bool
    {
        return Auth::user()?->can('create users') ?? false;
    }

    public static function canEdit(Model $record): bool
    {
        return Auth::user()?->can('update users') ?? false;
    }

    public static function canDelete(Model $record): bool
    {
        return Auth::user()?->can('delete users') ?? false;
    }

    public static function canDeleteAny(): bool
    {
        return Auth::user()?->can('delete users') ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                        Forms\Components\Hidden::make('company_id')
                    ->default(fn () => Auth::user()?->company_id),
                Forms\Components\Section::make('User Information')
                    ->schema([
  Forms\Components\Select::make('roles')
    ->relationship('roles', 'name')
    ->multiple()
    ->preload()
    ->required(),



                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Enter full name'),

                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->placeholder('user@example.com'),

                        Forms\Components\TextInput::make('password')
                            ->required()
                            ->placeholder('Enter password'),
                            

                       
                    ])
                    ->columns(2),
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

    Tables\Columns\TextColumn::make('roles.name')
    ->badge()
    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->icon('heroicon-o-user'),

                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-envelope')
                    ->copyable()
                    ->copyMessage('Email copied!')
                    ->copyMessageDuration(1500),

             

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->label('Joined')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
               
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->color('warning'),
                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation(),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}