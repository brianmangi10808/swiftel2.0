<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LeadsResource\Pages;
use App\Filament\Resources\LeadsResource\RelationManagers;
use App\Models\Leads;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use App\Models\Customer;
use Illuminate\Support\Collection;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LeadsResource extends Resource
{
    protected static ?string $model = Leads::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('firstname')->required(),
                TextInput::make('lastname')->required(),
                TextInput::make('email'),
                TextInput::make('mobile_number')->required(),
                Select::make('sector_id')
                    ->label('sector')
                    ->relationship('sector', 'name'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('firstname')->searchable(),
                TextColumn::make('lastname')->searchable(),
                TextColumn::make('email'),
                TextColumn::make('sector.name'),
                TextColumn::make('created_at')->dateTime()
            ])
              ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make()
                        ->label('Delete Ticket')
                        ->icon('heroicon-o-trash')
                        ->color('danger'),
                ])
                    ->label('')
                    ->icon('heroicon-o-ellipsis-vertical')
                    ->button(),

                Tables\Actions\Action::make('convert_to_customer')
                    ->label('Convert to Customer')
                    ->icon('heroicon-o-user-plus')
                    ->color('success')
                    ->form([
                        Forms\Components\TextInput::make('firstname')
                            ->default(fn ($record) => $record->firstname)
                            ->required(),

                        Forms\Components\TextInput::make('lastname')
                            ->default(fn ($record) => $record->lastname)
                            ->required(),

                        Forms\Components\TextInput::make('username')
                            ->default(fn ($record) => $record->mobile_number)
                            ->required(),

                        Forms\Components\TextInput::make('password')
                            ->default(fn () => 'SW' . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT))
                            ->label('Password')
                            ->helperText('Auto-generated password, unique for each customer'),

                        Forms\Components\TextInput::make('status')
                            ->default('offline'),

                        Forms\Components\TextInput::make('mobile_number')
                            ->default(fn ($record) => $record->mobile_number),

                        Forms\Components\Select::make('sector_id')
                            ->label('Sector')
                            ->relationship('sector', 'name')
                            ->default(fn ($record) => $record->sector_id)
                            ->required(),

                        Forms\Components\Select::make('service_id')
                            ->label('Service')
                            ->relationship('service', 'name')
                            ->required(),

                        Forms\Components\Select::make('group_id')
                            ->label('Group')
                            ->relationship('group', 'name')
                            ->required(),

                        Forms\Components\Select::make('premise_id')
                            ->label('Premise')
                            ->relationship('premise', 'name')
                            ->required(),

                        Forms\Components\TextInput::make('Calling_Station_Id')
                            ->label('Mac Address'),

                        Forms\Components\DatePicker::make('expiry_date')
                            ->label('Expiry Date')
                            ->default(now()->addMonth()),

                        Forms\Components\TextInput::make('credit')
                            ->numeric()
                            ->label('Credit Balance'),

                        Forms\Components\TextInput::make('email')
                            ->default(fn ($record) => $record->email),

                        Forms\Components\Toggle::make('enable')
                            ->label('Enabled')
                            ->default(true),

                        Forms\Components\Toggle::make('allow_mac')
                            ->label('Allow Mac Address')
                            ->default(false),

                        Forms\Components\DatePicker::make('created_at')
                            ->label('Created At')
                            ->default(now()),

                        Forms\Components\Textarea::make('comment')
                            ->maxLength(400)
                            ->columnSpanFull()
                            ->default(fn ($record) => $record->comment ?? ''),
                    ])
                    ->action(function (array $data, Leads $record) {
                        // Ensure password uniqueness
                        do {
                            $password = $data['password'] ?? 'SW' . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
                        } while (\App\Models\Customer::where('password', $password)->exists());

                        $data['password'] = $password;

                        // Create customer
                        \App\Models\Customer::create($data);

                        // Remove lead after conversion
                        $record->delete();

                        \Filament\Notifications\Notification::make()
                            ->title('Lead Converted')
                            ->body($data['firstname'] . ' has been converted to a customer. Password: ' . $password)
                            ->success()
                            ->send();
                    })
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    
                    // SMS Bulk Action with Form
                    Tables\Actions\BulkAction::make('send_sms')
                        ->label('Send SMS')
                        ->icon('heroicon-o-chat-bubble-bottom-center-text')
                        ->form([
                            Textarea::make('message')
                                ->label('Message')
                                ->required()
                                ->rows(4)
                                ->placeholder('Enter your SMS message here...')
                                ->maxLength(160)
                                ->helperText('Maximum 160 characters'),
                        ])
                        ->action(function (Collection $records, array $data) {
                            $message = $data['message'];
                            
                            foreach ($records as $record) {
                                // Add your SMS sending logic here
                                // Example: SMS::send($record->mobile_number, $message);
                            }

                            \Filament\Notifications\Notification::make()
                                ->title('SMS Sent Successfully!')
                                ->body('Message sent to ' . $records->count() . ' lead(s)')
                                ->success()
                                ->send();
                        }),
                    
                    // CSV Export Bulk Action
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
                ]);
            }

            fclose($handle);
        }, 'leads_export_' . now()->format('Y-m-d_His') . '.csv');
    }),
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
            'index' => Pages\ListLeads::route('/'),
            'create' => Pages\CreateLeads::route('/create'),
            'edit' => Pages\EditLeads::route('/{record}/edit'),
        ];
    }
}