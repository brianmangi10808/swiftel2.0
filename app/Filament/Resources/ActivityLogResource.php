<?php

namespace App\Filament\Resources;

use App\Models\ActivityLog;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Columns\KeyValueColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use App\Filament\Resources\ActivityLogResource\Pages;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;


class ActivityLogResource extends Resource
{
    protected static ?string $model = ActivityLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'System Logs';
    protected static ?string $navigationLabel = 'Activity Logs';
    protected static ?int $navigationSort = 50;

               public static function canViewAny(): bool
{
    return Auth::user()?->can('read activity_logs') ?? false;
}

public static function canView(Model $record): bool
{
    return Auth::user()?->can('read activity_logs') ?? false;
}

public static function canCreate(): bool
{
    return Auth::user()?->can('create activity_logs') ?? false;
}

public static function canEdit(Model $record): bool
{
    return Auth::user()?->can('update activity_logs') ?? false;
}

public static function canDelete(Model $record): bool
{
    return Auth::user()?->can('delete activity_logs') ?? false;
}

public static function canDeleteAny(): bool
{
    return Auth::user()?->can('delete activity_logs') ?? false;
}
    public static function getEloquentQuery(): Builder
{
    $query = parent::getEloquentQuery()
        ->where('action', '!=', 'viewed' ); // Exclude "viewed" actions globally

    $user = Auth::user();

    // If user is super admin → return query as-is (sees everything)
    if ($user?->is_super_admin) {
        return $query;
    }

    // Regular users → only see tickets from their own company
    return $query->where('company_id', $user->company_id);
}
    public static function table(Table $table): Table
    {
        return $table
        
            ->columns([
                //TextColumn::make('id')->sortable(),
                   TextColumn::make('company.name')
    ->label('Company')
    ->sortable()
    ->toggleable()
    ->visible(fn () => \Illuminate\Support\Facades\Auth::user()?->is_super_admin),

                TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('action')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                     'created' => 'success',
                        'updated' => 'warning',
                        'deleted' => 'danger',
                        //'action'  => 'primary',
                       // 'login'   => 'success',
                        'logout'  => 'danger',
                        default   => 'secondary',
                    })
                    ->sortable(),

                TextColumn::make('model')
                    ->label('Model')
                    ->searchable(),

               TextColumn::make('customer_name')
    ->label('Customer')
    ->sortable()
    ->searchable(),

    // TextColumn::make('model_name')
    // ->label('Record')
    // ->sortable()
    // ->searchable(),



Tables\Columns\ViewColumn::make('data')
    ->label('Changes')
    ->view('filament.tables.activity-log-changes'),

                TextColumn::make('ip_address')
                    ->label('IP')
                    ->sortable(),
                    

                TextColumn::make('created_at')
                    ->label('When')
                    ->dateTime()
                    ->sortable(),
            ])

            ->filters([
                SelectFilter::make('action')
                    ->label('Action Type')
                    ->options([
                        'created' => 'Created',
                        'updated' => 'Updated',
                        'deleted' => 'Deleted',
                        'viewed'  => 'Viewed',
                        'action'  => 'Action',
                        'login'   => 'Login',
                        'logout'  => 'Logout',
                    ]),

                Filter::make('date_range')
                    ->label('Date Range')
                    ->form([
                        Forms\Components\DatePicker::make('from'),
                        Forms\Components\DatePicker::make('to'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['from'], fn ($q) =>
                                $q->whereDate('created_at', '>=', $data['from'])
                            )
                            ->when($data['to'], fn ($q) =>
                                $q->whereDate('created_at', '<=', $data['to'])
                            );
                    }),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListActivityLogs::route('/'),
        'create' => Pages\CreateActivityLog::route('/create'),
        'edit' => Pages\EditActivityLog::route('/{record}/edit'),
        ];
    }
}
