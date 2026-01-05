<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceResource\Pages;
use App\Filament\Resources\ServiceResource\RelationManagers;
use App\Models\Service;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;

  
 protected static ?string $navigationIcon = 'heroicon-o-wifi';
    protected static ?string $navigationGroup = 'ISP Management';
    protected static ?string $navigationLabel = 'Services';
        protected static ?int $navigationSort = 40;
    protected static ?string $pluralModelLabel = 'Services';
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
                ->label('Service Name')
                ->placeholder('home basic ')
                ->required()
                ->maxLength(255),

            Forms\Components\TextInput::make('price')
                ->numeric()
                ->prefix('KES')
                ->required(),
 Forms\Components\TextInput::make('speed_limit')
                ->label('speed limit')
                ->placeholder('5M')
                ->required(),

            Forms\Components\TextInput::make('framed_pool')
                ->label('framed pool')
                ->placeholder('5M')
                ->required(),

            Forms\Components\TextInput::make('throttle_limit')
                ->label('throttle Speed')
                ->placeholder('5M')
                ->required(),

            Forms\Components\TextInput::make('fup_limit')
                ->numeric()
                ->suffix('GB')
                ->label('FUP Limit (GB)'),
                

            
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
             Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('price')->money('KES', true)->sortable(),
                Tables\Columns\TextColumn::make('speed_limit')->label('speed limit'),
                Tables\Columns\TextColumn::make('framed_pool')->label('framed pool'),
                Tables\Columns\TextColumn::make('throttle_limit')->label('throttle'),
                Tables\Columns\TextColumn::make('fup_limit')->label('FUP (GB)'),
                Tables\Columns\TextColumn::make('created_at')->date(),
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
            'index' => Pages\ListServices::route('/'),
            'create' => Pages\CreateService::route('/create'),
            'edit' => Pages\EditService::route('/{record}/edit'),
        ];
    }
}
