<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Models\Customer;
use Filament\Forms;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use App\Filament\Resources\CustomerResource\RelationManagers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Filament\Notifications\Notification;
use Illuminate\Validation\Rules\Unique;
use Filament\Tables\Columns\TextColumn;
use Filament\Support\Enums\FontWeight;
use Carbon\Carbon;
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
    public static function canViewAny(): bool
{
    return Auth::user()?->can('read customers') ?? false;
}

public static function canView(Model $record): bool
{
    return Auth::user()?->can('read customers') ?? false;
}

public static function canCreate(): bool
{
    return Auth::user()?->can('create customers') ?? false;
}

public static function canEdit(Model $record): bool
{
    return Auth::user()?->can('update customers') ?? false;
}

public static function canDelete(Model $record): bool
{
    return Auth::user()?->can('delete customers') ?? false;
}

public static function canDeleteAny(): bool
{
    return Auth::user()?->can('delete customers') ?? false;
}
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

                           
                     
                              

                            Forms\Components\DateTimePicker::make('expiry_date')
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

                                 Forms\Components\TextInput::make('simultaneous_use')
                                ->numeric()
                                ->label('simultaneous use ')
                                ->required(),

                                 Forms\Components\TextInput::make('Calling_Station_Id')
                                ->label('Mac Address'),
                                //->disabled(),

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
                                    'companyId' => $record->company_id,
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
                              'radacct' => $record?->radacct()->orderBy('radacctid', 'desc')->limit(20)->get() ?? collect([])
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
                'authlogs' => $record?->AuthLog()->whereNotNull('mac')->where('mac', '!=', '')->orderBy('created_at', 'desc')->limit(20)->get() ?? collect([])
            ]))
            ->columnSpanFull(),
    ]),
    Tabs\Tab::make('Extensions')
    ->icon('heroicon-m-clock')
    ->schema([
        Forms\Components\Placeholder::make('extensions ')
            ->content(fn ($record) => view('filament.tables.extensions-table', [
                'extensions' => $record?->extensions()->latest()->get() ?? collect([])
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
                Tables\Columns\TextColumn::make('is_new')
    ->label('')
     
     ->badge()
     
    ->getStateUsing(fn ($record) => $record->created_at->isAfter(now()->subMonth()) 
        ? 'New' 
        : ''
    )
    ->color(fn ($record) => $record->created_at->isAfter(now()->subMonth()) 
        ? 'success' 
        : null
    )
    ->tooltip(fn ($record) => $record->created_at->isAfter(now()->subMonth()) 
        ? 'New Customer (less than 1 month old)' 
        : null
    ),

                                      Tables\Columns\TextColumn::make('lastname')->sortable()->searchable() ->copyMessage('last name copied!')
                    ->copyMessageDuration(1500),
                Tables\Columns\TextColumn::make('username')->searchable() ->copyMessage('username copied!')
                    ->copyMessageDuration(1500),
               // Tables\Columns\TextColumn::make('status'),
               TextColumn::make('status')
                ->weight(FontWeight::Bold)
                ->badge()
    ->getStateUsing(function ($record) {
        // Determine if expired dynamically
        if (Carbon::parse($record->expiry_date)->isPast()) {
            return 'expired';
        }

        return $record->status; // online or offline
    })
    ->color(function ($state) {
        return match ($state) {
            'online' => 'success',   // green
            'offline' => 'danger',   // red
            'expired' => 'warning',  // yellow
            default => 'secondary',
        };
        
    }),
                 Tables\Columns\IconColumn::make('enable')->boolean()->label('Enabled'),
                Tables\Columns\TextColumn::make('sector.name')
    ->label('Sector'),
                 Tables\columns\TextColumn::make('service.name')
                 ->label('Service')  ->color('success') 
       ->weight(FontWeight::Bold),
                  Tables\columns\TextColumn::make('group.name')
                 ->label('Group'),
                Tables\Columns\TextColumn::make('credit')->label('Credit Balance')->sortable(),
              
                 Tables\Columns\TextColumn::make('expiry_date')->dateTime(),
                  
    
       
             
            ])
              ->defaultSort('id', 'desc')
            ->filters([
                   Tables\Filters\TrashedFilter::make(),

Tables\Filters\SelectFilter::make('sector_id')
    ->label('Sector')
    ->relationship('sector', 'name', function ($query) {
        $query->where('company_id',  Auth::user()->company_id);
    }),

       Tables\Filters\SelectFilter::make('group_id')
    ->label('Group')
    ->relationship('group', 'name', function ($query) {
        $query->where('company_id',  Auth::user()->company_id);
    }),

    Tables\Filters\SelectFilter::make('service_id')
    ->label('Service')
    ->relationship('service', 'name', function ($query) {
        $query->where('company_id',  Auth::user()->company_id);
    }),

      

          Tables\Filters\SelectFilter::make('status')
    ->label('Status')
    ->options([
        'offline' => 'offline',
        'online' => 'online',
        'expired' => 'expired',
    ])
    ->query(function (Builder $query, array $data) {
        $value = $data['value'] ?? null;

        if ($value === 'expired') {
            return $query->where('expiry_date', '<', now());
        }

        if ($value) {
            return $query->where('status', $value);
        }

        return $query;
    }),


      Tables\Filters\Filter::make('has_extensions')
        ->label('Has Extensions')
        ->query(fn (Builder $query): Builder => $query->has('extensions'))
        ->toggle(),
    
   
    Tables\Filters\Filter::make('no_extensions')
        ->label('No Extensions')
        ->query(fn (Builder $query): Builder => $query->doesntHave('extensions'))
        ->toggle(),
    
          Tables\Filters\SelectFilter::make('extension_count')
        ->label('Extension Usage')
        ->options([
            '1' => '1 Extension',
            '2' => '2 Extensions',
            '3' => '3 Extensions',
            '4+' => '4+ Extensions',
        ])
        ->query(function (Builder $query, array $data) {
            if (!isset($data['value'])) {
                return $query;
            }
            
            $value = $data['value'];
            
            if ($value === '4+') {
                return $query->has('extensions', '>=', 4);
            }
            
            return $query->has('extensions', '=', (int)$value);
        }),
    
        

     Tables\Filters\SelectFilter::make('expiry_date')
     ->label('Expiery ')
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

            
                Tables\Actions\ViewAction::make()->label(''),
                Tables\Actions\EditAction::make()->label(''),
                
                  Tables\Actions\ForceDeleteAction::make(),
            Tables\Actions\RestoreAction::make(),
            // Add this to your table actions in CustomerResource
Tables\Actions\Action::make('extend_expiry')
    ->label('Extend')
    ->icon('heroicon-o-clock')
    ->color('warning')
    ->form([
        Forms\Components\DatePicker::make('new_expiry_date')
            ->label('New Expiry Date')
            ->required()
            ->minDate(now()),
        Forms\Components\Textarea::make('reason')
            ->label('Reason for Extension')
            ->required()
            ->maxLength(500),
    ])
    ->action(function (Customer $record, array $data) {
        // Create extension record
        $record->extensions()->create([
            'old_expiry_date' => $record->expiry_date,
            'new_expiry_date' => $data['new_expiry_date'],
            'reason' => $data['reason'],
        ]);
        
        // Update customer's expiry date
        $record->update(['expiry_date' => $data['new_expiry_date']]);
        
        Notification::make()
            ->title('Expiry date extended')
            ->success()
            ->send();
    }),
              
            ])
            ->bulkActions([

            Tables\Actions\BulkAction::make('export')
    ->label('Export Customers')
    ->icon('heroicon-o-arrow-down-tray')
    ->action(function ($records) {
        return response()->streamDownload(function () use ($records) {
            $handle = fopen('php://output', 'w');
            
            // Add CSV headers
            fputcsv($handle, [
                'username',
                 'password',
                'sector_id',
                'service_id',
                'group_id',
                'premise_id',
                'enable',
                'firstname',
                'lastname',
                'mobile_number',
                'Created At',
                'expiry_date',
            ]);

            // Add data rows
            foreach ($records as $record) {
                fputcsv($handle, [
                    $record->username,
                    $record->password,
                    $record->sector_id,
                    $record->service_id,
                    $record->group_id,
                     $record->premise_id,
                      $record->enable,
                     $record->firstname,
                    $record->lastname,
                     $record->mobile_number,
                    $record->created_at?->format('Y-m-d H:i:s'),
                     $record->expiry_date?->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($handle);
        }, 'customer_export_' . now()->format('Y-m-d_His') . '.csv');
    }),

Tables\Actions\BulkActionGroup::make(array_merge(
        [
            Tables\Actions\DeleteBulkAction::make(),
           Tables\Actions\BulkAction::make('dropCredits')
    ->label('Drop Credits')
    ->icon('heroicon-o-currency-dollar')
    ->requiresConfirmation()
    ->form([
        Forms\Components\TextInput::make('amount')
            ->numeric()
            ->required()
            ->label('Credits to drop'),
    ])
    ->action(function ($records, array $data): void {
        foreach ($records as $record) {
            $record->credit = max(0, $record->credit - $data['amount']);
            $record->save();
        }
    }),

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