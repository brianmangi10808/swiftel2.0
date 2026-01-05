<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Models\Customer;
use Filament\Forms;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\DatePicker;
use App\Filament\Resources\CustomerResource\RelationManagers;
use Illuminate\Support\Facades\Auth;

use Filament\Tables;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Illuminate\Database\Eloquent\Builder;
use Filament\Notifications\Notification;
use Illuminate\Validation\Rules\Unique;
use Filament\Forms\Components\DateTimePicker;

use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Forms\Components\Tabs;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'Customers';
    protected static ?string $navigationGroup = 'Customers';
    protected static ?int $navigationSort = 1;

public static function getEloquentQuery(): Builder
{
    $query = parent::getEloquentQuery()
        ->withoutGlobalScopes([
            SoftDeletingScope::class,
        ]);

    $user = Auth::user();   
    if ($user->is_super_admin) {
        return $query;
    }
    return $query->where('company_id', $user->company_id);
}



    public static function form(Form $form): Form
    {
        return $form->schema([
            Tabs::make('CustomerTabs')
                ->tabs([
                    
                    Tabs\Tab::make('Customer Details')
                        ->icon('heroicon-m-user')
                        ->schema([
                           Forms\Components\Hidden::make('company_id')
                           ->reactive()
    ->default(fn () => Auth::user()?->company_id),


                             Forms\Components\TextInput::make('firstname')->required(),
                            Forms\Components\TextInput::make('lastname')->required(),
                       Forms\Components\TextInput::make('username')
                    ->label('Username')
                    ->required()
                    ->unique(
                        table: 'customers',              // or omit; Filament infers from model
                        column: 'username',
                        ignoreRecord: true,              // avoids false positives when editing
                        modifyRuleUsing: function (Unique $rule, callable $get) {
                            // Scope uniqueness to selected company_id
                            $companyId = $get('company_id'); // Filament-safe way to read another field
                            return $rule->where(fn ($query) => $query->where('company_id', $companyId));
                        },
                    )
                    ->helperText('Must be unique within the selected company.'),
                            Forms\Components\TextInput::make('password')                               
                                ->label('Password')
                                ->required(),                           
                           
                           
                            Forms\Components\TextInput::make('mobile_number')
                            ->required(),    
                            
                                                Forms\Components\Select::make('sector_id')
    ->label('Sector')
    ->relationship(
        name: 'sector',
        titleAttribute: 'name',
        modifyQueryUsing: function ($query) {
            $user = Auth::user(); 

            if (! $user?->is_super_admin) {
                $query->where('company_id', $user->company_id);
            }
        }
    )
    ->required(),

                             
                                                Forms\Components\Select::make('service_id')
    ->label('Service')
    ->relationship(
        name: 'service',
        titleAttribute: 'name',
        modifyQueryUsing: function ($query) {
            $user = Auth::user(); 

            if (! $user?->is_super_admin) {
                $query->where('company_id', $user->company_id);
            }
        }
    )
    ->required(),
     
                    
                  Forms\Components\Select::make('group_id')
    ->label('Group')
    ->relationship(
        name: 'group',
        titleAttribute: 'name',
        modifyQueryUsing: function ($query) {
            $user = Auth::user(); 

            if (! $user?->is_super_admin) {
                $query->where('company_id', $user->company_id);
            }
        }
    )
    ->required(),
                    Forms\Components\Select::make('premise_id')
    ->label('Premise')
    ->relationship(
        name: 'premise',
        titleAttribute: 'name',
        modifyQueryUsing: function ($query) {
            $user = Auth::user(); 

            if (! $user?->is_super_admin) {
                $query->where('company_id', $user->company_id);
            }
        }
    )
    ->required(),

                           
                     
                              

                            Forms\Components\DatePicker::make('expiry_date')
                                ->label('Expiry Date')
                                ->required()
                                 ->default(now()->addMonth()),

                            Forms\Components\TextInput::make('credit')
                                ->numeric()
                                ->label('Credit Balance')
                                ->required(),
                             Forms\Components\TextInput::make('email'),
                              Forms\Components\Toggle::make('enable')
                                ->label('Enabled')
                                ->default(true),

                               Forms\Components\Toggle::make('allow_mac')
                                ->label('Allow Mac Address')
                                ->default(true),

                                Forms\Components\DatePicker::make('created_at')
                                ->label('created_at')
                                 ->default(now()),
                            Forms\Components\Textarea::make('comment')
                                ->maxLength(400)
                                ->columnSpanFull(),

                              
                        ])
                        ->columns(2),

          Tabs\Tab::make('Monitoring')
                    ->icon('heroicon-m-chart-bar')
                    ->schema([
                        Forms\Components\Placeholder::make('live_traffic_graph')
                            ->label('Live Traffic Monitor')
                            ->content(function ($record) {
                                if (!$record || !$record->username) {
                                    return new \Illuminate\Support\HtmlString(
                                        '<div class="text-sm text-gray-500">No customer selected</div>'
                                    );
                                }
                                
                                             $pppoeInterface = urlencode("<pppoe-{$record->username}>");

                                
                                return view('filament.tables.live-traffic', [
                                    'pppoe' => $pppoeInterface,
                                ]);
                            })
                            ->columnSpanFull(),
                    ]),

         
                      Tabs\Tab::make('Payments')
                    ->icon('heroicon-m-banknotes')
                    ->schema([
                        Forms\Components\Placeholder::make('payments_table')
                            ->content(fn ($record) => view('filament.tables.payments-table', [
                                'payments' => $record?->payments ?? collect([])
                            ]))
                            ->columnSpanFull(),
                    ]),

 
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
               //  Tables\Columns\TextColumn::make('id')->searchable(),
                 Tables\Columns\TextColumn::make('company.name')
    ->label('Company')
    ->sortable()
    ->toggleable()
    ->visible(fn () => Auth::user()?->is_super_admin),

                  Tables\Columns\TextColumn::make('firstname')->sortable()->searchable() ->copyMessage('first copied!')
                    ->copyMessageDuration(1500),
                Tables\Columns\TextColumn::make('lastname')->sortable()->searchable() ->copyMessage('last name copied!')
                    ->copyMessageDuration(1500),
                Tables\Columns\TextColumn::make('username')->searchable() ->copyMessage('username copied!')
                    ->copyMessageDuration(1500),
                Tables\Columns\TextColumn::make('status'),
                 Tables\Columns\IconColumn::make('enable')->boolean()->label('Enabled'),
                Tables\Columns\TextColumn::make('sector.name')
    ->label('Sector'),
                 Tables\columns\TextColumn::make('service.name')
                 ->label('Service'),
                  Tables\columns\TextColumn::make('group.name')
                 ->label('Group'),
                Tables\Columns\TextColumn::make('credit')->label('Credit Balance'),
              
                 Tables\Columns\TextColumn::make('expiry_date')->dateTime(),
                  
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
Tables\Actions\BulkActionGroup::make(array_merge(
        [
            Tables\Actions\DeleteBulkAction::make(),
        ],
        static::generateSmsActions()
    )),

     

   
                Tables\Actions\RestoreBulkAction::make(),
            ]);
    }
 public static function generateSmsActions(): array
{
    $actions = [];
    $user = Auth::user();

    $templates = \App\Models\SmsTemplate::where('active', true)
        ->when(! $user->is_super_admin, function ($query) use ($user) {
            $query->where('company_id', $user->company_id);
        })
        ->get();

    foreach ($templates as $template) {

        $label = "Send " . ucfirst(str_replace('_', ' ', $template->type)) . " SMS";

        $actions[] = Tables\Actions\BulkAction::make("send_{$template->type}_sms")
            ->label($label)
            ->icon('heroicon-o-chat-bubble-left-right')
            ->color('success')
            ->requiresConfirmation()
            ->action(function ($records) use ($template) {

                $success = 0;
                $failed = 0;

                foreach ($records as $customer) {
                    // ✅ Hard safety check
                    if ($customer->company_id !== $template->company_id) {
                        $failed++;
                        continue;
                    }

                    $sent = $customer->sendSmsFromTemplate($template->type);
                    $sent ? $success++ : $failed++;
                }

                if ($success > 0) {
                    \Filament\Notifications\Notification::make()
                        ->title("SMS Sent")
                        ->body("{$success} messages sent using '{$template->type}' template.")
                        ->success()
                        ->send();
                }

                if ($failed > 0) {
                    \Filament\Notifications\Notification::make()
                        ->title("Sending Failed")
                        ->body("{$failed} failed. Cross-company block or gateway failure.")
                        ->danger()
                        ->send();
                }
            });
    }

    return $actions;
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