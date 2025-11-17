<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages;
use App\Models\Payment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\DatePicker;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationGroup = 'Finance';
    protected static ?string $navigationLabel = 'Payments';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('transaction_type')->maxLength(50)->disabled(),
         
            Forms\Components\TextInput::make('trans_amount')->numeric()->prefix('KSH')->disabled(),
            Forms\Components\TextInput::make('business_short_code')->maxLength(10)->disabled(),
            Forms\Components\TextInput::make('bill_ref_number')->maxLength(20)->disabled(),
        
            Forms\Components\TextInput::make('first_name')->maxLength(50)->disabled(),
      
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('trans_id')->label('Transaction ID')->searchable()->sortable()->disabled(),
            Tables\Columns\TextColumn::make('first_name')->label('First Name')->searchable()->disabled(),
          
            Tables\Columns\TextColumn::make('trans_amount')->label('Amount')->money('KES', true)->sortable()->toggleable()->disabled()->searchable(),
            Tables\Columns\TextColumn::make('bill_ref_number')->label('Bill Ref')->searchable(),
         
            Tables\Columns\TextColumn::make('created_at')->dateTime()->label('Created'),
        ])
        ->defaultSort('created_at', 'desc')
        ->filters([
             Tables\Filters\SelectFilter::make('created_at')
     ->label('Created')
      ->form([
        
        DatePicker::make('from'),
        DatePicker::make('until'),
    ])
     ->query(function ($query, array $data) {
        return $query
            ->when($data['from'], fn ($q) => $q->whereDate('created_at', '>=', $data['from']))
            ->when($data['until'], fn ($q) => $q->whereDate('created_at', '<=', $data['until']));
    })
        ])
        ->actions([
           // Tables\Actions\EditAction::make(),
           //Tables\Actions\DeleteAction::make(),
        ])
        ->bulkActions([
          //  Tables\Actions\DeleteBulkAction::make(),
            // CSV Export Bulk Action
Tables\Actions\BulkAction::make('export')
    ->label('Export Leads')
    ->icon('heroicon-o-arrow-down-tray')
    ->action(function ($records) {
        return response()->streamDownload(function () use ($records) {
            $handle = fopen('php://output', 'w');
            
            // Add CSV headers
            fputcsv($handle, [
                'ID',
                'First Name',
                'Transaction ID',
                'Amount',
                'Mobile Number',
                'Created At',
            ]);

            // Add data rows
            foreach ($records as $record) {
                fputcsv($handle, [
                    $record->id,
                    $record->first_name,
                    $record->trans_id,
                    $record->trans_amount,
                    $record->bill_ref_number,
                    $record->created_at?->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($handle);
        }, 'Payments_export_' . now()->format('Y-m-d_His') . '.csv');
    }),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPayments::route('/'),
            //'create' => Pages\CreatePayment::route('/create'),
            //'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }
}
