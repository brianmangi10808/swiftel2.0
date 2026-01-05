<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PremiseResource\Pages;
use App\Filament\Resources\PremiseResource\RelationManagers;
use App\Models\Premise;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PremiseResource extends Resource
{
    protected static ?string $model = Premise::class;
   protected static ?string $navigationGroup = 'Area';
    protected static ?string $navigationIcon = 'heroicon-o-home';
        protected static ?int $navigationSort = 32;

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
        return $form
            ->schema([
                                Forms\Components\Hidden::make('company_id')
    ->default(fn () => Auth::user()?->company_id),
                 Forms\Components\TextInput::make('name')
                ->label('Premise Name')
                ->required()
                ->maxLength(255),

            Forms\Components\Textarea::make('description')
                ->label('Description')
                ->rows(2)
                ->maxLength(500),

            Forms\Components\Select::make('status')
                ->options([
                    'active' => 'Active',
                    'inactive' => 'Inactive',
                ])
                ->default('active')
                ->required(),

            Forms\Components\Toggle::make('available')
                ->label('Available')
                ->default(true),
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
    ->visible(fn () => \Illuminate\Support\Facades\Auth::user()?->is_super_admin),
                 Tables\Columns\TextColumn::make('name')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('description')->limit(40),
                 Tables\Columns\TextColumn::make('status')
                    ->colors([
                        'success' => 'active',
                        'danger' => 'inactive',
                    ]),
                Tables\Columns\IconColumn::make('available')->boolean(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->label('Created'),
            ])
              ->defaultSort('id', 'desc')
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
            'index' => Pages\ListPremises::route('/'),
            'create' => Pages\CreatePremise::route('/create'),
            'edit' => Pages\EditPremise::route('/{record}/edit'),
        ];
    }
}
