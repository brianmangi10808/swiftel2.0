<?php

namespace App\Filament\Resources\Packages;

use App\Filament\Resources\Packages\Pages\CreatePackages;
use App\Filament\Resources\Packages\Pages\EditPackages;
use App\Filament\Resources\Packages\Pages\ListPackages;
use App\Filament\Resources\Packages\Pages\ViewPackages;
use App\Filament\Resources\Packages\Schemas\PackagesForm;
use App\Filament\Resources\Packages\Schemas\PackagesInfolist;
use App\Filament\Resources\Packages\Tables\PackagesTable;
use App\Models\Packages;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PackagesResource extends Resource
{
    protected static ?string $model = Packages::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedServer;

    protected static ?string $recordTitleAttribute = 'packages';

    public static function form(Schema $schema): Schema
    {
        return PackagesForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PackagesInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PackagesTable::configure($table);
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
            'index' => ListPackages::route('/'),
            'create' => CreatePackages::route('/create'),
            'view' => ViewPackages::route('/{record}'),
            'edit' => EditPackages::route('/{record}/edit'),
        ];
    }
}
