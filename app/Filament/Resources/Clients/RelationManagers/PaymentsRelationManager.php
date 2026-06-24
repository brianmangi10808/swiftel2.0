<?php

namespace App\Filament\Resources\Clients\RelationManagers;

use App\Filament\Resources\PaymentResource;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

class PaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'Payment';

    protected static ?string $relatedResource = PaymentResource::class;

    protected static ?string $title = 'Payment';
protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-credit-card';

    public function table(Table $table): Table
    {
        return $table;
    }
}