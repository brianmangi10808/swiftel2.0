<?php

namespace App\Filament\Resources\Clients\RelationManagers;

use App\Filament\Resources\RadacctResource;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

class SessionsRelationManager extends RelationManager
{
    protected static string $relationship = 'sessions';

    protected static ?string $relatedResource = RadacctResource::class;

    protected static ?string $title = 'Sessions';
protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-signal';

    public function table(Table $table): Table
    {
        return $table;
    }
}