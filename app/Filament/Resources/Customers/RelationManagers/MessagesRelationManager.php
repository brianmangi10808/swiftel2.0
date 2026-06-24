<?php

namespace App\Filament\Resources\Customers\RelationManagers;

use App\Filament\Resources\MessagesResource;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

class MessagesRelationManager extends RelationManager
{
    protected static string $relationship = 'Messages';

    protected static ?string $relatedResource = MessagesResource::class;

    protected static ?string $title = 'Messages';
protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-newspaper';

    public function table(Table $table): Table
    {
        return $table;
    }
}