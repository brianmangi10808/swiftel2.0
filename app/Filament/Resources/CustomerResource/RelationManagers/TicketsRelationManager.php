<?php

namespace App\Filament\Resources\CustomerResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;

class TicketsRelationManager extends RelationManager
{
    protected static string $relationship = 'tickets';

    public function form(Form $form): Form
    {
        return $form
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
                            ->required(),

                        Forms\Components\Select::make('severity')
                            ->label('Priority')
                            ->options([
                                'low' => 'Low',
                                'medium' => 'Medium',
                                'high' => 'High',
                            ])
                            ->default('medium')
                            ->required(),
                    ]),

                Forms\Components\Textarea::make('description')
                    ->label('Description')
                    ->required()
                    ->rows(4)
                    ->placeholder('Describe in detail the issue that the client is facing.'),

                Forms\Components\Checkbox::make('notify_sms')
                    ->label('Notify user by SMS that a ticket has been raised.')
                    ->default(false),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('ticket_number')
            ->columns([
                Tables\Columns\TextColumn::make('ticket_number')
                    ->label('Ticket #')
                    ->searchable(),

                Tables\Columns\TextColumn::make('description')
                    ->label('Description')
                    ->limit(50)
                    ->searchable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'open' => 'warning',
                        'closed' => 'success',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('severity')
                    ->label('Priority')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'high' => 'danger',
                        'medium' => 'warning',
                        'low' => 'info',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Raise Ticket')
                    ->icon('heroicon-o-plus')
                    ->slideOver()
                    ->modalWidth('2xl')
                    ->mutateFormDataUsing(function (array $data): array {
                        // Add customer_id automatically
                        $data['customer_id'] = $this->ownerRecord->id;
                        return $data;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->slideOver()
                    ->modalWidth('2xl'),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}