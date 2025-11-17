<?php

namespace App\Filament\Resources\CustomerResource\Pages;

use App\Filament\Resources\CustomerResource;
use App\Models\Customer;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListCustomers extends ListRecords
{
    protected static string $resource = CustomerResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All')
             ->icon('heroicon-o-users')
                ->badge(Customer::count()),
                
            'online' => Tab::make('Online')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'online'))
                 ->icon('heroicon-o-signal')
                ->badge(Customer::where('status', 'online')->count()),
            
            'offline' => Tab::make('Offline')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'offline'))
                ->icon('heroicon-o-signal-slash')
                ->badge(Customer::where('status', 'offline')->count()),

            'enabled' => Tab::make('Enabled')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('enable', true))
                  ->icon('heroicon-o-check-circle')
                ->badge(Customer::where('enable', true)->count()),

            'disabled' => Tab::make('Disabled')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('enable', false))
                  ->icon('heroicon-o-x-circle')
                ->badge(Customer::where('enable', false)->count()),

            'expiring_24h' => Tab::make('Expiring ≤ 24h')
             ->icon('heroicon-o-clock')
                ->modifyQueryUsing(function (Builder $query) {
                    return $query->whereNotNull('expiry_date')
                                 ->whereBetween('expiry_date', [now(), now()->addDay()]);
                })
                ->badge(Customer::whereNotNull('expiry_date')
                    ->whereBetween('expiry_date', [now(), now()->addDay()])
                    ->count()),
        ];
    }

    public function getDefaultActiveTab(): string|int|null
    {
        return 'all';
    }
}