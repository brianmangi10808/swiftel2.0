<?php

namespace App\Filament\Resources\CustomerResource\Pages;

use App\Filament\Resources\CustomerResource;
use App\Models\Tickets;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Notifications\Notification;
use Filament\Forms;

class ViewCustomer extends ViewRecord
{
    protected static string $resource = CustomerResource::class;

    public function getTitle(): string
    {
        return $this->record->username . ' (Currently ' . ($this->record->status ?? 'offline') . ')';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\ActionGroup::make([
                // Edit Customer
                Actions\EditAction::make()
                    ->label('Edit Customer')
                    ->icon('heroicon-o-pencil')
                    ->color('primary'),

                // Send Expiry Message
         

                // Send Custom Message
           

                // Create Ticket - UPDATED VERSION
                Actions\Action::make('createTicket')
                    ->label('Raise Ticket')
                    ->icon('heroicon-o-ticket')
                    ->color('warning')
                    ->slideOver()
                    ->modalWidth('2xl')
                    ->modalHeading('Raise Ticket for ' . $this->record->firstname . ' ' . $this->record->lastname)
                    ->form([
                        // Display customer info (read-only)
                        Forms\Components\Section::make('Customer Information')
                            ->schema([
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\Placeholder::make('customer_name')
                                            ->label('Customer Name')
                                            ->content($this->record->firstname . ' ' . $this->record->lastname),

                                        Forms\Components\Placeholder::make('customer_status')
                                            ->label('Customer Status')
                                            ->content(fn () => new \Illuminate\Support\HtmlString(
                                                '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ' .
                                                ($this->record->status === 'online' 
                                                    ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' 
                                                    : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200') . '">' .
                                                ucfirst($this->record->status ?? 'offline') .
                                                '</span>'
                                            )),
                                    ]),

                                Forms\Components\Placeholder::make('sector')
                                    ->label('Sector')
                                    ->content($this->record->sector?->name ?? 'N/A'),
                            ])
                            ->collapsible(),

                        // Ticket fields to fill
                        Forms\Components\Section::make('Ticket Details')
                            ->schema([
                                Forms\Components\TextInput::make('ticket_number')
                                    ->label('Ticket Number')
                                    ->default(fn () => 'TICKET-' . rand(1000, 9999))
                                    ->disabled()
                                    ->dehydrated(),

                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\Select::make('status')
                                            ->label('Status')
                                            ->options([
                                                'open' => 'Open',
                                                'closed' => 'Closed',
                                            ])
                                            ->default('open')
                                            ->required()
                                            ->native(false),

                                        Forms\Components\Select::make('severity')
                                            ->label('Priority')
                                            ->options([
                                                'Low' => 'Low',
                                                'Medium' => 'Medium',
                                                'High' => 'High',
                                            ])
                                            ->default('Medium')
                                            ->required()
                                            ->native(false),
                                    ]),

                                Forms\Components\Textarea::make('description')
                                    ->label('Description')
                                    ->required()
                                    ->rows(5)
                                    ->placeholder('Describe in detail the issue that the client is facing.')
                                    ->helperText('Please provide as much detail as possible about the issue.'),

                                Forms\Components\Checkbox::make('notify_sms')
                                    ->label('Notify customer by SMS that a ticket has been raised.')
                                    ->default(false)
                                    ->inline(false),
                            ]),
                    ])
                    ->action(function (array $data) {
                        // Create the ticket with auto-filled customer_id
                        $ticket = Tickets::create([
                            'customer_id' => $this->record->id, // Auto-fill customer ID
                            'ticket_number' => $data['ticket_number'],
                            'description' => $data['description'],
                            'status' => $data['status'],
                            'severity' => $data['severity'],
                        ]);

                        // Send SMS if checkbox is checked
                      if ($data['notify_sms'] ?? false) {
                      $ticket->sendTicketSms('ticket_created');
                         }


                        // Show success notification with link to view ticket
                        Notification::make()
                            ->success()
                            ->title('Ticket Created Successfully')
                            ->body('Ticket #' . $ticket->id . ' has been created for ' . $this->record->firstname . ' ' . $this->record->lastname)
                            ->actions([
                                \Filament\Notifications\Actions\Action::make('view')
                                    ->label('View Ticket')
                                    ->button()
                                    ->url(route('filament.admin.resources.tickets.index')),
                            ])
                            ->duration(5000)
                            ->send();
                    }),

                // Manage Credit
                Actions\Action::make('manageCredit')
                    ->label('Manage Credit')
                    ->icon('heroicon-o-banknotes')
                    ->color('secondary')
                    ->form([
                        Forms\Components\TextInput::make('credit')
                            ->numeric()
                            ->required()
                            ->label('Credit Amount'),
                    ])
                    ->action(function (array $data) {
                        $this->record->update(['credit' => $data['credit']]);
                        Notification::make()
                            ->title('Customer Credit Updated')
                            ->success()
                            ->send();
                    }),
            ])
            ->label('Actions')
            ->icon('heroicon-o-ellipsis-vertical')
            ->color('success')
            ->button(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [];
    }

    // Set max content width to full
    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }
}