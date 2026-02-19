<?php

namespace App\Filament\Resources;

use App\Models\SmsTemplate;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class SmsTemplateResource extends Resource
{
    protected static ?string $model = SmsTemplate::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    protected static ?string $navigationLabel = 'SMS Templates';
      protected static ?int $navigationSort = 20;
                  public static function canViewAny(): bool
{
    return Auth::user()?->can('read sms_templates') ?? false;
}

public static function canView(Model $record): bool
{
    return Auth::user()?->can('read sms_templates') ?? false;
}

public static function canCreate(): bool
{
    return Auth::user()?->can('create sms_templates') ?? false;
}

public static function canEdit(Model $record): bool
{
    return Auth::user()?->can('update sms_templates') ?? false;
}

public static function canDelete(Model $record): bool
{
    return Auth::user()?->can('delete sms_templates') ?? false;
}

public static function canDeleteAny(): bool
{
    return Auth::user()?->can('delete sms_templates') ?? false;
}
    protected static ?string $navigationGroup = 'Communication';

public static function getEloquentQuery(): Builder
{
    $query = parent::getEloquentQuery();
    $user = Auth::user();

    // ✅ Super Admin → sees all tickets
    if ($user?->is_super_admin) {
        return $query;
    }

    // ✅ Company users → only their company tickets
    return $query->where('company_id', $user->company_id);
}

   public static function form(Form $form): Form
{
    return $form->schema([
                     Forms\Components\Hidden::make('company_id')
    ->default(fn () => Auth::user()?->company_id),
        Forms\Components\Select::make('type')
            ->label('Template Type')
            ->required()
            ->options([
                'expiry' => 'Expiry Notification',
                'welcome' => 'Welcome Message',
                'payment' => 'Payment Confirmation',
                'mac_warning' => 'MAC Address Warning',
                'general' => 'General SMS',
                'ticket_resolved' => 'Ticket Resolved',
                'ticket_created' => 'Ticket Created',
                'prune_notice' => 'Prune Notice',
            ])
            ->reactive(),

        Forms\Components\Textarea::make('template')
            ->label('Message Body')
            ->rows(6)
            ->helperText('Available placeholders: {firstname}, {lastname}, {username}, {expiry_date}, {service}, {sector}, {group}')
            ->required(),

        Forms\Components\Toggle::make('active')
            ->label('Enable This Template')
            ->default(true),
    ]);
}


    public static function table(Table $table): Table
    {
        return $table->columns([
               Tables\Columns\TextColumn::make('company.name')
    ->label('Company')
    ->sortable()
    ->toggleable()
    ->visible(fn () => Auth::user()?->is_super_admin),
            Tables\Columns\TextColumn::make('type')->searchable(),
            Tables\Columns\TextColumn::make('template')->limit(30),
            Tables\Columns\ToggleColumn::make('active'),
            Tables\Columns\TextColumn::make('updated_at')->dateTime(),
        ])
        ->actions([
            Tables\Actions\EditAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => SmsTemplateResource\Pages\ListSmsTemplates::route('/'),
            'create' => SmsTemplateResource\Pages\CreateSmsTemplate::route('/create'),
            'edit' => SmsTemplateResource\Pages\EditSmsTemplate::route('/{record}/edit'),
        ];
    }
}
