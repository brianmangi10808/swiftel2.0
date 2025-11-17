<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SmsProviderResource\Pages;
use App\Filament\Resources\SmsProviderResource\RelationManagers;
use App\Models\SmsProvider;
use App\Services\SmsProviders\SmsProviderFactory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Support\Exceptions\Halt;

class SmsProviderResource extends Resource
{
    protected static ?string $model = SmsProvider::class;

    protected static ?string $navigationIcon = 'heroicon-o-device-phone-mobile';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?string $navigationLabel = 'SMS Providers';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Provider Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->label('Provider Name')
                            ->placeholder('e.g., Africa\'s Talking Production')
                            ->helperText('A friendly name to identify this provider'),

                        Forms\Components\Select::make('provider_type')
                            ->required()
                            ->options(SmsProvider::getAvailableTypes())
                            ->label('Provider Type')
                            ->live()
                            ->afterStateUpdated(fn (Forms\Set $set) => $set('configuration', []))
                            ->helperText('Select the SMS service provider'),

                        Forms\Components\Textarea::make('description')
                            ->maxLength(65535)
                            ->columnSpanFull()
                            ->rows(2)
                            ->placeholder('Optional description for this provider'),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->helperText('Enable or disable this provider'),

                        Forms\Components\Toggle::make('is_default')
                            ->label('Set as Default')
                            ->default(false)
                            ->helperText('Use this provider as the default for sending SMS'),
                    ])->columns(2),

                Forms\Components\Section::make('Provider Configuration')
                    ->schema(function (Get $get) {
                        $providerType = $get('provider_type');

                        if (!$providerType) {
                            return [
                                Forms\Components\Placeholder::make('select_provider')
                                    ->label('')
                                    ->content('Please select a provider type above to configure settings.'),
                            ];
                        }

                        $availableProviders = SmsProviderFactory::getAvailableProviders();

                        if (!isset($availableProviders[$providerType])) {
                            return [];
                        }

                        $providerClass = $availableProviders[$providerType];
                        $instance = new $providerClass([]);
                        $configFields = $instance->getConfigFields();

                        $formFields = [];

                        foreach ($configFields as $key => $field) {
                            $formField = match ($field['type']) {
                                'password' => Forms\Components\TextInput::make("configuration.{$key}")
                                    ->password()
                                    ->revealable(),
                                'textarea' => Forms\Components\Textarea::make("configuration.{$key}")
                                    ->rows(3),
                                default => Forms\Components\TextInput::make("configuration.{$key}"),
                            };

                            $formField
                                ->label($field['label'] ?? ucfirst($key))
                                ->required($field['required'] ?? false)
                                ->placeholder($field['placeholder'] ?? '')
                                ->helperText($field['help'] ?? '');

                            if (isset($field['maxlength'])) {
                                $formField->maxLength($field['maxlength']);
                            }

                            if (isset($field['default'])) {
                                $formField->default($field['default']);
                            }

                            $formFields[] = $formField;
                        }

                        return $formFields;
                    })
                    ->columns(2),

                Forms\Components\Section::make('Connection Test')
                    ->schema([
                        Forms\Components\Actions::make([
                            Forms\Components\Actions\Action::make('test_connection')
                                ->label('Test Connection & Check Balance')
                                ->icon('heroicon-o-signal')
                                ->color('success')
                                ->requiresConfirmation(false)
                                ->action(function (Get $get, Forms\Set $set) {
                                    $providerType = $get('provider_type');
                                    $configuration = $get('configuration');

                                    if (!$providerType) {
                                        Notification::make()
                                            ->title('Provider Type Required')
                                            ->body('Please select a provider type first.')
                                            ->warning()
                                            ->send();
                                        throw new Halt();
                                    }

                                    if (!$configuration || empty(array_filter($configuration))) {
                                        Notification::make()
                                            ->title('Configuration Required')
                                            ->body('Please fill in the provider configuration fields first.')
                                            ->warning()
                                            ->send();
                                        throw new Halt();
                                    }

                                    $availableProviders = SmsProviderFactory::getAvailableProviders();

                                    if (!isset($availableProviders[$providerType])) {
                                        Notification::make()
                                            ->title('Invalid Provider')
                                            ->body('The selected provider type is not valid.')
                                            ->danger()
                                            ->send();
                                        throw new Halt();
                                    }

                                    try {
                                        $providerClass = $availableProviders[$providerType];
                                        $provider = new $providerClass($configuration);
                                        $result = $provider->testConnection();

                                        if ($result['success']) {
                                            $bodyHtml = '<div style="font-size: 14px;">';
                                            $bodyHtml .= '<p style="margin-bottom: 10px;">' . $result['message'] . '</p>';

                                            if (isset($result['data']['balance'])) {
                                                $bodyHtml .= '<div style="background-color: #10b981; color: white; padding: 12px; border-radius: 6px; font-weight: bold; font-size: 16px; text-align: center; margin-top: 10px;">';
                                                $bodyHtml .= '💰 Balance: ' . $result['data']['balance'];
                                                $bodyHtml .= '</div>';
                                            }

                                            if (isset($result['data']) && count($result['data']) > 1) {
                                                $bodyHtml .= '<div style="margin-top: 12px; padding: 10px; background-color: #f3f4f6; border-radius: 6px;">';
                                                foreach ($result['data'] as $key => $value) {
                                                    if ($key !== 'balance') {
                                                        $bodyHtml .= '<div style="margin: 4px 0;"><strong>' . ucfirst(str_replace('_', ' ', $key)) . ':</strong> ' . $value . '</div>';
                                                    }
                                                }
                                                $bodyHtml .= '</div>';
                                            }

                                            $bodyHtml .= '</div>';

                                            Notification::make()
                                                ->title('✅ Connection Successful!')
                                                ->body(new \Illuminate\Support\HtmlString($bodyHtml))
                                                ->success()
                                                ->duration(10000)
                                                ->send();
                                        } else {
                                            Notification::make()
                                                ->title('❌ Connection Failed')
                                                ->body($result['message'])
                                                ->danger()
                                                ->duration(8000)
                                                ->send();
                                        }
                                    } catch (\Exception $e) {
                                        Notification::make()
                                            ->title('Error Testing Connection')
                                            ->body('An error occurred: ' . $e->getMessage())
                                            ->danger()
                                            ->send();
                                        throw new Halt();
                                    }
                                }),
                        ])
                        ->fullWidth(),

                        Forms\Components\Placeholder::make('test_info')
                            ->label('')
                            ->content('Click the button above to test the connection with your provider credentials and view your account balance.')
                            ->extraAttributes(['class' => 'text-sm text-gray-600']),
                    ])
                    ->visible(fn (Get $get) => $get('provider_type') !== null)
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('provider_type')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => SmsProviderFactory::getProviderName($state))
                    ->color(fn (string $state): string => match ($state) {
                        'simflix' => 'info',
                        'africastalking' => 'success',
                        'twilio' => 'warning',
                        'suftech' => 'primary',
                        default => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_default')
                    ->boolean()
                    ->label('Default')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('provider_type')
                    ->options(SmsProvider::getAvailableTypes())
                    ->label('Provider Type'),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active')
                    ->placeholder('All providers')
                    ->trueLabel('Active only')
                    ->falseLabel('Inactive only'),

                Tables\Filters\TernaryFilter::make('is_default')
                    ->label('Default')
                    ->placeholder('All providers')
                    ->trueLabel('Default only')
                    ->falseLabel('Non-default only'),
            ])
            ->actions([
                Tables\Actions\Action::make('test')
                    ->label('Test & Check Balance')
                    ->icon('heroicon-o-signal')
                    ->color('success')
                    ->action(function (SmsProvider $record) {
                        $provider = SmsProviderFactory::makeFromDbProvider($record);

                        if (!$provider) {
                            Notification::make()
                                ->title('Error')
                                ->body('Failed to initialize provider')
                                ->danger()
                                ->send();
                            return;
                        }

                        $result = $provider->testConnection();

                        if ($result['success']) {
                            $bodyHtml = '<div style="font-size: 14px;">';
                            $bodyHtml .= '<p style="margin-bottom: 10px;">' . $result['message'] . '</p>';

                            if (isset($result['data']['balance'])) {
                                $bodyHtml .= '<div style="background-color: #10b981; color: white; padding: 12px; border-radius: 6px; font-weight: bold; font-size: 16px; text-align: center; margin-top: 10px;">';
                                $bodyHtml .= '💰 Balance: ' . $result['data']['balance'];
                                $bodyHtml .= '</div>';
                            }

                            if (isset($result['data']) && count($result['data']) > 1) {
                                $bodyHtml .= '<div style="margin-top: 12px; padding: 10px; background-color: #f3f4f6; border-radius: 6px;">';
                                foreach ($result['data'] as $key => $value) {
                                    if ($key !== 'balance') {
                                        $bodyHtml .= '<div style="margin: 4px 0;"><strong>' . ucfirst(str_replace('_', ' ', $key)) . ':</strong> ' . $value . '</div>';
                                    }
                                }
                                $bodyHtml .= '</div>';
                            }

                            $bodyHtml .= '</div>';

                            Notification::make()
                                ->title('✅ Connection Successful!')
                                ->body(new \Illuminate\Support\HtmlString($bodyHtml))
                                ->success()
                                ->duration(10000)
                                ->send();
                        } else {
                            Notification::make()
                                ->title('❌ Connection Failed')
                                ->body($result['message'])
                                ->danger()
                                ->duration(8000)
                                ->send();
                        }
                    }),

                Tables\Actions\EditAction::make(),

                Tables\Actions\DeleteAction::make()
                    ->action(function (SmsProvider $record) {
                        if ($record->is_default) {
                            Notification::make()
                                ->title('Cannot Delete')
                                ->body('Cannot delete the default provider. Please set another provider as default first.')
                                ->danger()
                                ->send();
                            return;
                        }

                        $record->delete();

                        Notification::make()
                            ->title('Deleted')
                            ->body('Provider deleted successfully')
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->action(function ($records) {
                            $defaultProvider = $records->firstWhere('is_default', true);

                            if ($defaultProvider) {
                                Notification::make()
                                    ->title('Cannot Delete')
                                    ->body('Cannot delete the default provider. Please set another provider as default first.')
                                    ->danger()
                                    ->send();
                                return;
                            }

                            $records->each->delete();
                        }),
                ]),
            ])
            ->defaultSort('is_default', 'desc');
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
            'index' => Pages\ListSmsProviders::route('/'),
            'create' => Pages\CreateSmsProvider::route('/create'),
            'edit' => Pages\EditSmsProvider::route('/{record}/edit'),
        ];
    }
}
