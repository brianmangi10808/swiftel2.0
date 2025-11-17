<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Customer;

class IspOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $total = Customer::count();
        $online = Customer::where('status', 'online')->count();
        $offline = Customer::where('status', 'offline')->count();
        $expired = Customer::where('expiry_date', '<', now())->count();

        return [
            Stat::make('Total Customers', $total)
                ->color('primary')
                ->description('All registered clients'),

            Stat::make('Online Users', $online)
                ->color('success')
                ->description('Active PPPoE sessions'),

            Stat::make('Offline Users', $offline)
                ->color('danger')
                ->description('Currently not connected'),

            Stat::make('Expired Accounts', $expired)
                ->color('warning')
                ->description('Needs renewal'),
        ];
    }
}
