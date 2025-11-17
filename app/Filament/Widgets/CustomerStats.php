<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\ExpirySnapshot;

class CustomerStats extends BaseWidget
{
   // protected static ?string $heading = 'Customer Health Stats';

    protected function getStats(): array
    {
        $today = now()->startOfDay();
        $yesterday = now()->subDay()->startOfDay();

        $todaySnapshot = ExpirySnapshot::where('snapshot_date', $today)->first();
        $yesterdaySnapshot = ExpirySnapshot::where('snapshot_date', $yesterday)->first();

        if (!$todaySnapshot || !$yesterdaySnapshot) {
            return [
                Stat::make('Active Users', 'No Data'),
                Stat::make('Retention Rate', 'No Data'),
                Stat::make('Churn Rate', 'No Data'),
            ];
        }

        // Daily churn = expired yesterday but not renewed today
        $churn = max($yesterdaySnapshot->new_expiries_today - $todaySnapshot->renewed_today, 0);

        // churn rate
        $churnRate = $yesterdaySnapshot->active_users > 0
            ? round(($churn / $yesterdaySnapshot->active_users) * 100, 2)
            : 0;

        // retention rate
        $retentionRate = $yesterdaySnapshot->new_expiries_today > 0
            ? round(($todaySnapshot->renewed_today / $yesterdaySnapshot->new_expiries_today) * 100, 2)
            : 0;

        return [
            Stat::make('Active Users', $todaySnapshot->active_users),

            Stat::make('Retention Rate', $retentionRate . '%')
                ->description('Expired but renewed'),

            Stat::make('Churn Rate', $churnRate . '%')
                ->description('Expired and not renewed'),
        ];
    }
}
