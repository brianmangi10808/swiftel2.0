<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Customer;
use App\Models\ExpirySnapshot;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CustomerLineStats extends ChartWidget
{
    protected static ?string $heading = 'Customer Trends';
    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $months = collect(range(5, 0))
            ->map(function ($i) {
                return Carbon::now()->subMonths($i);
            });

        $labels = $months->map(fn($d) => $d->format('M Y'));

        // ✅ NEW CUSTOMERS per month
        $newCustomers = $months->map(function ($month) {
            return Customer::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();
        });

        // ✅ RENEWALS per month (expiry_date increased)
        $renewals = $months->map(function ($month) {
            return Customer::whereYear('expiry_date', $month->year)
                ->whereMonth('expiry_date', $month->month)
                ->whereRaw('DATE(expiry_date) != DATE(created_at)')
                ->count();
        });

        // ✅ CHURN per month (users expired & not renewed)
        $churned = $months->map(function ($month) {
            return Customer::whereDate('expiry_date', '<=', $month->endOfMonth())
                ->whereDoesntHave('payments')   // no renewal
                ->count();
        });

        // ✅ ACTIVE USERS (from your ExpirySnapshot table)
        $activeUsers = $months->map(function ($month) {
            $snap = ExpirySnapshot::whereDate('snapshot_date', $month->format('Y-m-d'))->first();
            return $snap->active_users ?? 0;
        });

        // ✅ RETENTION RATE:
        //     retention = (1 - churn / (active last month)) * 100
        $retention = collect();
        for ($i = 0; $i < count($activeUsers); $i++) {
            $lastActive = $activeUsers[$i - 1] ?? $activeUsers[$i];
            $churn = $churned[$i];

            $rate = $lastActive == 0 ? 0 : round((1 - ($churn / $lastActive)) * 100, 1);
            $retention->push($rate);
        }

        return [
            'datasets' => [
                [
                    'label' => 'New Customers',
                    'data' => $newCustomers,
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'transparent',
                    'yAxisID' => 'y',
                ],
                [
                    'label' => 'Renewals',
                    'data' => $renewals,
                    'borderColor' => '#22c55e',
                    'backgroundColor' => 'transparent',
                    'yAxisID' => 'y',
                ],
                [
                    'label' => 'Churned',
                    'data' => $churned,
                    'borderColor' => '#ef4444',
                    'backgroundColor' => 'transparent',
                    'yAxisID' => 'y',
                ],
                [
                    'label' => 'Retention Rate (%)',
                    'data' => $retention,
                    'borderColor' => '#fbbf24',
                    'backgroundColor' => 'transparent',
                    'borderDash' => [5,5],
                    'yAxisID' => 'y1',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'type' => 'linear',
                    'position' => 'left',
                    'title' => ['display' => true, 'text' => 'Customers'],
                    'beginAtZero' => true,
                ],
                'y1' => [
                    'type' => 'linear',
                    'position' => 'right',
                    'title' => ['display' => true, 'text' => 'Retention %'],
                    'beginAtZero' => true,
                    'grid' => ['drawOnChartArea' => false],
                ],
            ],
        ];
    }
}
