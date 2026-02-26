<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DeviceResource\Pages;
use App\Filament\Resources\DeviceResource\RelationManagers;
use App\Models\Device;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Illuminate\Database\Eloquent\Model;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use RouterOS\Client;
use RouterOS\Query;


class DeviceResource extends Resource
{
    protected static ?string $model = Device::class;
      protected static ?int $navigationSort = 41;
    protected static ?string $navigationIcon = 'heroicon-o-server';
    protected static ?string $navigationGroup = 'Network Devices';
    protected static ?string $navigationLabel = 'MikroTik Devices';

    public static function canViewAny(): bool
{
    return Auth::user()?->can('read devices') ?? false;
}

public static function canView(Model $record): bool
{
    return Auth::user()?->can('read devices') ?? false;
}

public static function canCreate(): bool
{
    return Auth::user()?->can('create devices') ?? false;
}

public static function canEdit(Model $record): bool
{
    return Auth::user()?->can('update devices') ?? false;
}

public static function canDelete(Model $record): bool
{
    return Auth::user()?->can('delete devices') ?? false;
}

public static function canDeleteAny(): bool
{
    return Auth::user()?->can('delete devices') ?? false;
}
public static function getEloquentQuery(): Builder
{
    $query = parent::getEloquentQuery();
    $user = Auth::user();

    // Super Admin → sees all devices
    if ($user?->is_super_admin) {
        return $query;
    }

    // Company users → only their company devices
    return $query->where('company_id', $user->company_id);
}

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                                Forms\Components\Hidden::make('company_id')
    ->default(fn () => Auth::user()?->company_id),
                 Forms\Components\TextInput::make('nasname')
                ->required()
                ->label('ip address')
                ->placeholder('e.g., 102.0.24.21'),

            Forms\Components\TextInput::make('api_username')
                ->label('api username')
                ->required()
                ->placeholder('e.g., admin'),

            Forms\Components\TextInput::make('shortname')
                ->label('shortname')
                ->required(),

            Forms\Components\TextInput::make('secret')
                ->label('secret/password')
               
                ->required(),

            Forms\Components\TextInput::make('location')
                ->label('Location')
                ->placeholder('e.g Data Center')
                ->maxLength(255),
                     Forms\Components\TextInput::make('type')
                ->label('type')
                ->placeholder('e.g Data Center')
                ->maxLength(255),
                     Forms\Components\TextInput::make('server')
                ->label('server')
                ->placeholder('e.g Data Center')
                ->maxLength(255),
            
            Forms\Components\TextInput::make('api_port')
                ->numeric()
                ->default(8728)
                ->label('API Port'),    

            
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
                 Tables\Columns\TextColumn::make('nasname')->sortable()->searchable()->label('ip_address'),
            Tables\Columns\TextColumn::make('shortname')->sortable(),
            Tables\Columns\TextColumn::make('location')->limit(30) ->label('AREA CODE'),
            Tables\Columns\TextColumn::make('status')
            ->badge()
                ->colors([
                    'success' => 'online',
                    'danger'  => 'offline',
                ]),
                Tables\Columns\TextColumn::make('api_port'),
            Tables\Columns\TextColumn::make('created_at')->dateTime()->label('Added'),
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
            'index' => Pages\ListDevices::route('/'),
            'create' => Pages\CreateDevice::route('/create'),
            'edit' => Pages\EditDevice::route('/{record}/edit'),
        ];
    }
}
