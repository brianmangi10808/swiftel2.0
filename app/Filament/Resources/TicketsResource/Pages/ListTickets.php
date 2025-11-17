<?php

namespace App\Filament\Resources\TicketsResource\Pages;

use App\Filament\Resources\TicketsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;


use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListTickets extends ListRecords
{
    protected static string $resource = TicketsResource::class;


 protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Raise ticket')
                ->icon('heroicon-o-plus')
                ->color('success')
                ->slideOver()  // Enable slide-over
                ->modalWidth('5xl'),  // Half screen width (options: sm, md, lg, xl, 2xl, 3xl, 4xl, 5xl, 6xl, 7xl)
        ];
    }
  public function getTabs(): array
    {
        return [
            'open' => Tab::make('Open Tickets')
                ->icon('heroicon-o-ticket')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'open')),
            
            'closed' => Tab::make('Closed Tickets')
                ->icon('heroicon-o-check-circle')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'closed')),
        ];
    }

}
