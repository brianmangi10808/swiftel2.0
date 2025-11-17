<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Models\Customer;
use Filament\Forms;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\DatePicker;
use App\Filament\Resources\CustomerResource\RelationManagers;

use Filament\Tables;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Illuminate\Database\Eloquent\Builder;

use Filament\Forms\Components\DateTimePicker;

use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Forms\Components\Tabs;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'Customers';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Tabs::make('CustomerTabs')
                ->tabs([
                    // 🟢 Customer Details
                    Tabs\Tab::make('Customer Details')
                        ->icon('heroicon-m-user')
                        ->schema([
                             Forms\Components\TextInput::make('firstname'),
                            Forms\Components\TextInput::make('lastname'),
                            Forms\Components\TextInput::make('username')
                                ->required(),
                            Forms\Components\TextInput::make('password')                               
                                ->label('Password'),                           
                              Forms\Components\TextInput::make('status'),
                           
                            Forms\Components\TextInput::make('mobile_number'),                      
                        Forms\Components\Select::make('sector_id')
    ->label('Sector')
    ->relationship('sector', 'name')
    ->required(),
                          Forms\Components\Select::make('service_id')
                                 ->label('Service')
                                 ->relationship('service','name')
                                 ->required(),
                           
                          Forms\Components\Select::make('group_id')
                                 ->label('Group')
                                 ->relationship('group','name')
                                 ->required(),
                            Forms\Components\Select::make('premise_id')
                                 ->label('premise')
                                 ->relationship('Premise','name')
                                 ->required(),
                            Forms\Components\TextInput::make('Calling_Station_Id')
                                 ->label('Mac Address'),
                              
                              

                            Forms\Components\DatePicker::make('expiry_date')
                                ->label('Expiry Date'),

                            Forms\Components\TextInput::make('credit')
                                ->numeric()
                                ->label('Credit Balance'),
                             Forms\Components\TextInput::make('email'),
                              Forms\Components\Toggle::make('enable')
                                ->label('Enabled'),
                               Forms\Components\Toggle::make('allow_mac')
                                ->label('Allow Mac Address'), 
                                Forms\Components\DatePicker::make('created_at')
                                ->label('created_at'),
                            Forms\Components\Textarea::make('comment')
                                ->maxLength(400)
                                ->columnSpanFull(),

                              
                        ])
                        ->columns(2),

                    //  Payments Tab
         
                      Tabs\Tab::make('Payments')
                    ->icon('heroicon-m-banknotes')
                    ->schema([
                        Forms\Components\Placeholder::make('payments_table')
                            ->content(fn ($record) => view('filament.tables.payments-table', [
                                'payments' => $record?->payments ?? collect([])
                            ]))
                            ->columnSpanFull(),
                    ]),

  // Radacct Tab
                Tabs\Tab::make('Sessions')
                    ->icon('heroicon-m-chart-bar')
                    ->schema([
                        Forms\Components\Placeholder::make('radacct_table')
                            ->content(fn ($record) => view('filament.tables.radacct-table', [
                                'radacct' => $record?->radacct ?? collect([])
                            ]))
                            ->columnSpanFull(),
                    ]),

                   
Tabs\Tab::make('Messages')
    ->icon('heroicon-m-chat-bubble-left-right')
    ->schema([
        Forms\Components\Placeholder::make('messages_table')
            ->content(fn ($record) => view('filament.tables.messages-table', [
                'messages' => $record?->messages ?? collect([])
            ]))
            ->columnSpanFull(),
    ]),
                      
          Tabs\Tab::make('Logs')
    ->icon('heroicon-m-information-circle')
    ->schema([
        Forms\Components\Placeholder::make('authlog_table')
            ->content(fn ($record) => view('filament.tables.authlog-table', [
                'authlogs' => $record?->AuthLog()->whereNotNull('mac')->where('mac', '!=', '')->get() ?? collect([])
            ]))
            ->columnSpanFull(),
    ]),
                  
                ])
                ->persistTabInQueryString()
                ->columnSpanFull()
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                 Tables\Columns\TextColumn::make('id')->searchable(),
                  Tables\Columns\TextColumn::make('firstname')->sortable(),
                Tables\Columns\TextColumn::make('lastname')->sortable(),
                Tables\Columns\TextColumn::make('username')->searchable(),
                Tables\Columns\TextColumn::make('status'),
                 Tables\Columns\IconColumn::make('enable')->boolean()->label('Enabled'),
                Tables\Columns\TextColumn::make('sector.name')
    ->label('Sector'),
                 Tables\columns\TextColumn::make('service.name')
                 ->label('Service'),
                  Tables\columns\TextColumn::make('group.name')
                 ->label('Group'),
                Tables\Columns\TextColumn::make('credit')->label('Credit Balance'),
              
                 Tables\Columns\TextColumn::make('expiry_date'),
                  
              //  Tables\Columns\TextColumn::make('expiry_date')->required(),
            ])
              ->defaultSort('id', 'desc')
            ->filters([
                   Tables\Filters\TrashedFilter::make(),
       Tables\Filters\SelectFilter::make('sector_id')
        ->label('Sector')
        ->relationship('sector', 'name'),
        Tables\Filters\SelectFilter::make('group_id')
        ->label('Group')
        ->relationship('group', 'name'),
        Tables\Filters\SelectFilter::make('service_id')
        ->label('Service')
        ->relationship('service', 'name'),
         Tables\Filters\SelectFilter::make('status')
    ->label('Status')
    ->options([
        'offline' => 'offline',
        'online' => 'online',
        'expired' => 'expired',
    ]),
     Tables\Filters\SelectFilter::make('expiry_date')
     ->label('Expiery')
      ->form([
        
        DatePicker::make('from'),
        DatePicker::make('until'),
    ])
     ->query(function ($query, array $data) {
        return $query
            ->when($data['from'], fn ($q) => $q->whereDate('expiry_date', '>=', $data['from']))
            ->when($data['until'], fn ($q) => $q->whereDate('expiry_date', '<=', $data['until']));
    })

            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                  Tables\Actions\ForceDeleteAction::make(),
            Tables\Actions\RestoreAction::make(),
              
            ])
            ->bulkActions([
               Tables\Actions\DeleteBulkAction::make(),
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
                'Last Name',
                'Email',
                'Mobile Number',
                'Sector',
                'Created At',
                'Expiry Date'
                
            ]);

            // Add data rows
            foreach ($records as $record) {
                fputcsv($handle, [
                    $record->id,
                    $record->firstname,
                    $record->lastname,
                    $record->email,
                    $record->mobile_number,
                    $record->sector?->name ?? '',
                    $record->created_at?->format('Y-m-d H:i:s'),
                    $record->expiry_date?->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($handle);
        }, 'customer_export_' . now()->format('Y-m-d_His') . '.csv');
    }),
                Tables\Actions\RestoreBulkAction::make(),
            ]);
    }
public static function getEloquentQuery(): Builder
{
    return parent::getEloquentQuery()
        ->withoutGlobalScopes([
            SoftDeletingScope::class,
        ]);
}
public static function getRelations(): array
{
    return [
        RelationManagers\TicketsRelationManager::class,
    ];
}
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
            'view' => Pages\ViewCustomer::route('/{record}'),
        ];
    }
}