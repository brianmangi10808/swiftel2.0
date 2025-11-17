<?php
// app/Filament/Resources/CustomerResource/RelationManagers/PaymentsRelationManager.php
namespace App\Filament\Resources\CustomerResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class PaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'payments';
    protected static ?string $title = 'Payments'; // tab label

    // Optional: badge on the tab
    // public static function getBadge($ownerRecord): ?string
    // {
    //     return (string) $ownerRecord->payments()->count();
    // }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('trans_id')->label('Transaction ID')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('trans_amount')->label('Amount')->money('KES', true)->sortable(),
                Tables\Columns\TextColumn::make('trans_time')->label('Time')->dateTime()->sortable(),
                Tables\Columns\TextColumn::make('bill_ref_number')->label('Bill Ref')->searchable(),
            ])
            ->defaultSort('trans_time', 'desc');
    }
}
