<?php
namespace App\Filament\Resources\SectorResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Support\Enums\FontWeight;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Filament\Notifications\Notification;

class CustomersRelationManager extends RelationManager{
protected static string $relationship = 'customers';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                

                Forms\Components\Grid::make(2)
                    ->schema([
                       Forms\Components\TextInput::make('firstname')
                    ->label('firstname'),
                     Forms\Components\TextInput::make('lastname')
                    ->label('lastname'),
                    ]),

                Forms\Components\Textarea::make('comment')
                    ->label('comment')
                    ->required()
                    ->rows(4)
                    ->placeholder('Describe in detail the issue that the client is facing.'),

                
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('firstname')->sortable()->searchable() ->copyMessage('first copied!')
                    ->copyMessageDuration(1500),
                Tables\Columns\TextColumn::make('lastname')->sortable()->searchable() ->copyMessage('last name copied!')
                    ->copyMessageDuration(1500),
                Tables\Columns\TextColumn::make('username')->searchable() ->copyMessage('username copied!')
                    ->copyMessageDuration(1500),
             
               Tables\Columns\TextColumn::make('status')
                ->weight(FontWeight::Bold)
                ->badge()
    ->getStateUsing(function ($record) {

        if (Carbon::parse($record->expiry_date)->isPast()) {
            return 'expired';
        }

        return $record->status; // online or offline
    })
    ->color(function ($state) {
        return match ($state) {
            'online' => 'success',   // green
            'offline' => 'danger',   // red
            'expired' => 'warning',  // yellow
            default => 'secondary',
        };
        
    }),
                 Tables\Columns\IconColumn::make('enable')->boolean()->label('Enabled'),
                Tables\Columns\TextColumn::make('sector.name')
    ->label('Sector'),
                 Tables\columns\TextColumn::make('service.name')
                 ->label('Service'),
                  Tables\columns\TextColumn::make('group.name')
                 ->label('Group'),
                Tables\Columns\TextColumn::make('credit')->label('Credit Balance'),
              
                 Tables\Columns\TextColumn::make('expiry_date')->dateTime(),
            ])
            ->filters([
                 Tables\Filters\TrashedFilter::make(),



       Tables\Filters\SelectFilter::make('group_id')
    ->label('Group')
    ->relationship('group', 'name', function ($query) {
        $query->where('company_id',  Auth::user()->company_id);
    }),

    Tables\Filters\SelectFilter::make('service_id')
    ->label('Service')
    ->relationship('service', 'name', function ($query) {
        $query->where('company_id',  Auth::user()->company_id);
    }),

      

         Tables\Filters\SelectFilter::make('status')
    ->label('Status')
    ->options([
        'offline' => 'offline',
        'online' => 'online',
        'expired' => 'expired',
    ]),

      Tables\Filters\Filter::make('has_extensions')
        ->label('Has Extensions')
        ->query(fn (Builder $query): Builder => $query->has('extensions'))
        ->toggle(),
    
   
    Tables\Filters\Filter::make('no_extensions')
        ->label('No Extensions')
        ->query(fn (Builder $query): Builder => $query->doesntHave('extensions'))
        ->toggle(),
    
          Tables\Filters\SelectFilter::make('extension_count')
        ->label('Extension Usage')
        ->options([
            '1' => '1 Extension',
            '2' => '2 Extensions',
            '3' => '3 Extensions',
            '4+' => '4+ Extensions',
        ])
        ->query(function (Builder $query, array $data) {
            if (!isset($data['value'])) {
                return $query;
            }
            
            $value = $data['value'];
            
            if ($value === '4+') {
                return $query->has('extensions', '>=', 4);
            }
            
            return $query->has('extensions', '=', (int)$value);
        }),
    
        

     Tables\Filters\SelectFilter::make('expiry_date')
     ->label('Expiery ')
      ->form([
        
        DatePicker::make('from'),
        DatePicker::make('until'),
    ])
     ->query(function ($query, array $data) {
        return $query
            ->when($data['from'], fn ($q) => $q->whereDate('expiry_date', '>=', $data['from']))
            ->when($data['until'], fn ($q) => $q->whereDate('expiry_date', '<=', $data['until']));
    })
            ])
            ->headerActions([
        
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