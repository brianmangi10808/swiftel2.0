<?php

namespace App\Filament\Resources\CustomerResource\Pages;

use App\Filament\Resources\CustomerResource;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables;
use Filament\Tables\Table;

class ManageCustomerPayments extends ManageRelatedRecords
{
    protected static string $resource = CustomerResource::class;

    // ✅ Must stay typed *and initialized* to avoid both PHP 8.3 and Filament errors
    protected static string $relationship = 'payments';

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationLabel = 'Payments';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('trans_id')
            ->columns([
                Tables\Columns\TextColumn::make('trans_id')
                    ->label('Transaction ID')
                    ->sortable(),

                Tables\Columns\TextColumn::make('trans_amount')
                    ->label('Amount')
                    ->money('KES', true)
                    ->sortable(),

                Tables\Columns\TextColumn::make('trans_time')
                    ->label('Time')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('bill_ref_number')
                    ->label('Bill Ref')
                    ->searchable(),
            ])
            ->defaultSort('trans_time', 'desc');
    }
}
