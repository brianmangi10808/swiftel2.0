<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TicketsResource\Pages;
use App\Models\Tickets;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Support\Enums\FontWeight;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\CustomerResource;
use Illuminate\Database\Eloquent\Model;

class TicketsResource extends Resource
{
    protected static ?string $model = Tickets::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Tickets';
    protected static ?string $pluralLabel = 'Tickets';
     protected static ?int $navigationSort = 3;
      protected static ?string $navigationGroup = 'Customers';
    protected static ?string $modelLabel = 'Ticket';
public static function canViewAny(): bool
{
    return Auth::user()?->can('read tickets') ?? false;
}

public static function canView(Model $record): bool
{
    return Auth::user()?->can('read tickets') ?? false;
}

public static function canCreate(): bool
{
    return Auth::user()?->can('create tickets') ?? false;
}

public static function canEdit(Model $record): bool
{
    return Auth::user()?->can('update tickets') ?? false;
}

public static function canDelete(Model $record): bool
{
    return Auth::user()?->can('delete tickets') ?? false;
}

public static function canDeleteAny(): bool
{
    return Auth::user()?->can('delete tickets') ?? false;
}

public static function getEloquentQuery(): Builder
{
    $query = parent::getEloquentQuery();
    $user = Auth::user();

    // ✅ Super Admin → sees all tickets
    if ($user?->is_super_admin) {
        return $query;
    }

    // ✅ Company Ticket → only their company tickets
    return $query->where('company_id', $user->company_id);
}
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
             Forms\Components\Hidden::make('company_id')
    ->default(fn () => Auth::user()?->company_id),
                    Forms\Components\Select::make('customer_id')
    ->label('Customer')
    ->relationship(
        name: 'customer',
        titleAttribute: 'firstname',
  modifyQueryUsing: fn ($query) => $query
            ->when(
                ! Auth::user()?->is_super_admin,
                fn ($q) => $q->where('company_id', Auth::user()->company_id)
            )
            ->select(['id', 'firstname', 'lastname'])
    )
    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->firstname} {$record->lastname}")
->searchable()
->preload()
    ->required(),

                  
              

                Forms\Components\Select::make('severity')
                    ->label('Severity')
                    ->options([
                        'Low' => 'Low',
                        'Medium' => 'Medium',
                        'High' => 'High',
                    ])
                    ->required(),

                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'open' => 'Open',
                        'closed' => 'Closed',
                    ])
                    ->required(),

                Forms\Components\Textarea::make('description')
                    ->label('Description')
                    ->maxLength(400)
                    ->required(),
Forms\Components\Textarea::make('resolution_notes')
                ->label('Resolution Notes')
                ->disabled()  // Make it read-only on view
                ->visible(fn ($record) => $record?->status === 'closed')
                ->rows(4),
                Forms\Components\Textarea::make('comment')
                    ->label('Comment')
                    ->columnSpanFull()
                    ->maxLength(400),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('company.name')
    ->label('Company')
    ->sortable()
    ->toggleable()
    ->visible(fn () => Auth::user()?->is_super_admin),

                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

            Tables\Columns\TextColumn::make('customer.full_name')
    ->label('Customer')
    ->getStateUsing(fn ($record) => $record->customer?->firstname . ' ' . $record->customer?->lastname)
    ->weight(FontWeight::Bold)
    ->color('dark')
    ->url(fn ($record) => $record->customer ? CustomerResource::getUrl('view', ['record' => $record->customer->id]) : null),
  Tables\Columns\TextColumn::make('customer.status')
                ->label('Customer Status')
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'online' => 'success',
                    'offline' => 'primary',
                    default => 'gray',
                })
                ->sortable(),
                    Tables\Columns\TextColumn::make('customer.sector.name')
                    ->label('Sector')
                    ->sortable()
                    ->searchable(),

             

                Tables\Columns\TextColumn::make('description')
                    ->label('Description')
                    ->limit(30)
                    ->searchable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->color(fn (string $state): string => match ($state) {
                    'open' => 'success',
                     'Open' => 'success',
                    'closed' => 'danger',
                    'Closed' => 'danger',
                    default => 'gray',
                })
                    ->badge(),

                Tables\Columns\TextColumn::make('severity')
                    ->label('Severity')
                    ->color(fn (string $state): string => match ($state) {
                    'Low' => 'success',
                     'Medium' => 'primary',
                    'High' => 'danger',
                    
                    default => 'gray',
                })
                    ->badge(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
     

            ])
             ->defaultSort('id', 'desc')
            ->filters([
                //
            ])
 
    ->actions([
            Tables\Actions\Action::make('resolve')
                ->label('Resolve')
                ->button()
                ->color('success')
                ->visible(fn (Tickets $record): bool => $record->status === 'open')
                ->form([
                    Forms\Components\Textarea::make('resolution_notes')
                        ->label('How was this issue resolved?')
                        ->required()
                        ->rows(4)
                        ->placeholder('Enter resolution details...'),
                    
                    Forms\Components\Checkbox::make('send_sms')
                        ->label('Send SMS to client notifying them that the ticket has been resolved.')
                        ->default(false),
                ])
                ->action(function (Tickets $record, array $data) {
                    $record->status = 'closed';
                    $record->resolution_notes = $data['resolution_notes'];
                    $record->resolved_at = now();
                    $record->save();

                    // If send_sms is checked, trigger SMS notification
                   if ($data['send_sms'] ?? false) {
        $record->sendTicketSms('ticket_resolved');
    }

                    
                }),

            Tables\Actions\ActionGroup::make([
        
                Tables\Actions\DeleteAction::make()
                    ->label('Delete Ticket')
                    ->icon('heroicon-o-trash')
                    ->color('danger'),
            ])
            ->label('')
            ->icon('heroicon-o-ellipsis-vertical')
            ->button(),

                  // ADD THIS: View Action as Modal
            Tables\Actions\Action::make('view')
                ->label('View')
                ->icon('heroicon-o-eye')
                ->color('info')
                ->modalHeading('Ticket Details')
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Close')
                ->slideOver()  // Optional: Makes it slide from right
                ->modalWidth('4xl')  // Half screen width
                ->fillForm(fn (Tickets $record): array => [
                  
                    'customer' => $record->customer?->firstname . ' ' . $record->customer?->lastname,
                    'customer_status' => $record->customer?->status,
                    'sector' => $record->customer?->sector?->name,
                    'description' => $record->description,
                    'status' => $record->status,
                    'severity' => $record->severity,
                    'created_at' => $record->created_at,
                    'resolution_notes' => $record->resolution_notes,
                    'resolved_at' => $record->resolved_at,
                ])
                ->form([
                    Forms\Components\Section::make('Ticket Information')
                        ->schema([
                           

                            Forms\Components\Grid::make(2)
                                ->schema([
                                    Forms\Components\TextInput::make('customer')
                                        ->label('Customer')
                                        ->disabled(),

                                    Forms\Components\TextInput::make('customer_status')
                                        ->label('Customer Status')
                                        ->disabled(),
                                ]),

                            Forms\Components\TextInput::make('sector')
                                ->label('Sector')
                                ->disabled(),

                            Forms\Components\Grid::make(2)
                                ->schema([
                                    Forms\Components\TextInput::make('status')
                                        ->label('Status')
                                        ->disabled(),

                                    Forms\Components\TextInput::make('severity')
                                        ->label('Priority')
                                        ->disabled(),
                                ]),

                            Forms\Components\Textarea::make('description')
                                ->label('Description')
                                ->disabled()
                                ->rows(4),

                            Forms\Components\TextInput::make('created_at')
                                ->label('Created At')
                                ->disabled(),
                        ]),

                    Forms\Components\Section::make('Resolution Details')
                        ->schema([
                            Forms\Components\Textarea::make('resolution_notes')
                                ->label('Resolution Notes')
                                ->disabled()
                                ->rows(4),

                            Forms\Components\TextInput::make('resolved_at')
                                ->label('Resolved At')
                                ->disabled(),
                        ])
                        ->visible(fn (Tickets $record): bool => $record->status === 'closed'),
                ]),

        ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListTickets::route('/'),
          // 'create' => Pages\CreateTickets::route('/create'),
           // 'edit' => Pages\EditTickets::route('/{record}/edit'),
            //'view' => Pages\ViewTickets::route('/{record}'),
        ];
    }
}
