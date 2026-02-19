<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SmsGatewayResource\Pages;
use App\Filament\Resources\SmsGatewayResource\RelationManagers;
use App\Models\SmsGateway;
use Filament\Forms;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleColumn;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Resources\Resource;
use App\Models\Company;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class SmsGatewayResource extends Resource
{
    protected static ?string $model = SmsGateway::class;
 
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Communication';
        protected static ?int $navigationSort = 21;

        public static function canViewAny(): bool
{
    return Auth::user()?->can('read sms_gateways') ?? false;
}

public static function canView(Model $record): bool
{
    return Auth::user()?->can('read sms_gateways') ?? false;
}

public static function canCreate(): bool
{
    return Auth::user()?->can('create sms_gateways') ?? false;
}

public static function canEdit(Model $record): bool
{
    return Auth::user()?->can('update sms_gateways') ?? false;
}

public static function canDelete(Model $record): bool
{
    return Auth::user()?->can('delete sms_gateways') ?? false;
}

public static function canDeleteAny(): bool
{
    return Auth::user()?->can('delete sms_gateways') ?? false;
}
    public static function getEloquentQuery(): Builder
{
    $query = parent::getEloquentQuery();
    $user = Auth::user();

   
    if ($user?->is_super_admin) {
        return $query;
    }

   
    return $query->where('company_id', $user->company_id);
}
public static function form(Form $form): Form
{
    return $form
        ->schema([
            Section::make('Gateway Details')
                ->schema([
    //                     Forms\Components\Hidden::make('company_id')
    // ->default(fn () => Auth::user()?->company_id),
    Forms\Components\Select::make('company_id')
    ->label('Company')
    ->options(Company::pluck('name', 'id'))
    ->required()
    ->visible(fn () => Auth::user()?->is_super_admin)   // Only super admin sees it
    ->default(fn () => Auth::user()?->company_id),

Forms\Components\Hidden::make('company_id')
    ->default(fn () => Auth::user()?->company_id)
    ->visible(fn () => !Auth::user()?->is_super_admin),
                    TextInput::make('name')
                        ->required()
                        ->label('Gateway Name'),

                    Select::make('type')
                        ->label('Gateway Type')
                        ->required()
                        ->options([
                            'simflix' => 'SimFlix',
                            'africastalking' => 'Africa\'s Talking',
                            'afrokatt' => 'Afrokatt',
                            'custom' => 'Custom / Manual Config',
                        ])
                        ->reactive(),
                                            Toggle::make('is_active')
    ->label('Active Gateway')
    ->default(false)
    ->reactive()
    ->helperText('Only one gateway can be active at a time.'),

                ]),

            Section::make('Credentials (Dynamic)')
                ->schema(function (callable $get) {

                    $type = $get('type');

                    // Define field sets
                    $fields = [

                        'simflix' => [
                            TextInput::make('credentials.sender_id')->required(),
                            TextInput::make('credentials.api_key')->required(),
                            TextInput::make('credentials.url')->required(),
                        ],

                        'africastalking' => [
                            TextInput::make('credentials.username')->required(),
                            TextInput::make('credentials.api_key')->required(),
                            TextInput::make('credentials.sender_id')->required(),
                        ],

                        'afrokatt' => [
                            TextInput::make('credentials.api_key')->required(),
                            TextInput::make('credentials.sender_id')->required(),
                        ],

                        'custom' => [
                            Forms\Components\KeyValue::make('credentials')
                                ->label('Custom Key/Value Inputs')
                                ->helperText('Add any fields required for the custom gateway.')
                              ,
                        ],
                    ];

                    return $fields[$type] ?? [];
                })
                ->reactive()
                ->columns(2),
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
              Tables\Columns\TextColumn::make('name')->searchable(),

            Tables\Columns\BadgeColumn::make('type')
                ->colors([
                    'primary',
                    'success' => 'simflix',
                    'warning' => 'africastalking',
                    'danger' => 'afrokatt',
                ])
                ->formatStateUsing(fn ($state) => ucfirst($state)),

    Tables\Columns\IconColumn::make('is_active')
    ->label('Active Gateway')
    ->boolean(),





            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            
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
            'index' => Pages\ListSmsGateways::route('/'),
            'create' => Pages\CreateSmsGateway::route('/create'),
            'edit' => Pages\EditSmsGateway::route('/{record}/edit'),
        ];
    }
}
