<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DeviceResource\Pages;
use App\Filament\Resources\DeviceResource\RelationManagers;
use App\Models\Device;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
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
public static function getEloquentQuery(): Builder
{
    $query = parent::getEloquentQuery();
    $user = Auth::user();

    // Super Admin → sees all tickets
    if ($user?->is_super_admin) {
        return $query;
    }

    // Company users → only their company tickets
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
                ->label('Device Name')
                ->placeholder('e.g., CCR2116 Main Router'),

            Forms\Components\TextInput::make('ip_address')
                ->label('IP Address')
                ->required()
                ->placeholder('e.g., 192.168.88.1'),

            Forms\Components\TextInput::make('username')
                ->label('Username')
                ->required(),

            Forms\Components\TextInput::make('password')
                ->label('Password')
                ->password()
                ->required(),

            Forms\Components\TextInput::make('location')
                ->label('Location')
                ->placeholder('e.g Data Center')
                ->maxLength(255),
            
            Forms\Components\TextInput::make('api_port')
                ->numeric()
                ->default(8728)
                ->label('API Port'),    

            Forms\Components\Select::make('status')
                ->label('Status')
                ->options([
                    'online' => 'Online',
                    'offline' => 'Offline',
                ])
                ->default('offline'),
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
            Tables\Columns\TextColumn::make('ip_address')->sortable(),
            Tables\Columns\TextColumn::make('location')->limit(30),
            Tables\Columns\TextColumn::make('status')
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
                 Tables\Actions\Action::make('testConnection')
        ->label('Test Connection')
        ->icon('heroicon-o-wifi')
        ->button()
        ->color('info')
        ->action(function ($record) {
            try {
                $client = new \RouterOS\Client([
                    'host'    => $record->ip_address,
                    'user'    => $record->username,
                    'pass'    => $record->password,
                    'port'    => $record->api_port,
                    'timeout' => 3,
                ]);

                // Ask the router its identity (a harmless API query)
                $query = new \RouterOS\Query('/system/identity/print');
                $response = $client->query($query)->read();

                $identity = $response[0]['name'] ?? 'Unknown';
                $record->update(['status' => 'online']);

                \Filament\Notifications\Notification::make()
                    ->title("{$record->name} is ONLINE")
                    ->body("Connected successfully as '{$identity}'")
                    ->success()
                    ->send();
            } catch (\Throwable $e) {
                $record->update(['status' => 'offline']);

                \Filament\Notifications\Notification::make()
                    ->title("{$record->name} is OFFLINE")
                    ->body("Error: {$e->getMessage()}")
                    ->danger()
                    ->send();
            }
        }),
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
