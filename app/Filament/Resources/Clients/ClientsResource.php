<?php

namespace App\Filament\Resources\Clients;

use App\Filament\Resources\Clients\Pages\CreateClients;
use App\Filament\Resources\Clients\Pages\EditClients;
use App\Filament\Resources\Clients\Pages\ListClients;
use App\Filament\Resources\Clients\Pages\ViewClients;
use App\Filament\Resources\Clients\Schemas\ClientsForm;
use App\Filament\Resources\Clients\Schemas\ClientsInfolist;
use App\Filament\Resources\Clients\Tables\ClientsTable;
use App\Models\Clients;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ClientsResource extends Resource
{
    protected static ?string $model = Clients::class;
 protected static \UnitEnum|string|null $navigationGroup = 'HOTSPOT Clients';
 
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSignalSlash;

    protected static ?string $recordTitleAttribute = 'Hotspot Customers';

    public static function form(Schema $schema): Schema
    {
        return ClientsForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ClientsInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ClientsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
                    RelationManagers\SessionsRelationManager::class,
                    RelationManagers\PaymentsRelationManager::class, 
                    RelationManagers\MessagesRelationManager::class, 


        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListClients::route('/'),
            'create' => CreateClients::route('/create'),
            'view' => ViewClients::route('/{record}'),
            'edit' => EditClients::route('/{record}/edit'),
        ];
    }
}
