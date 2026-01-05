<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Customer;
use App\Models\ExpirySnapshot;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CustomerLineStats extends ChartWidget
{
    protected static ?string $heading = 'Customer Trends';
    protected static ?int $sort = 2;

protected function getData(): array
{
     $user = Auth::user();

    $months = collect(range(5, 0))->map(fn ($i) => Carbon::now()->subMonths($i));
    $labels = $months->map(fn($d) => $d->format('M Y'));

    // ✅ BASE QUERY WITH MULTI-COMPANY SECURITY
    $baseCustomerQuery = Customer::query()
        ->when(!$user->is_super_admin, function ($q) use ($user) {
            $q->where('company_id', $user->company_id);
        });

    // ✅ NEW CUSTOMERS per month
    $newCustomers = $months->map(function ($month) use ($baseCustomerQuery) {
        return (clone $baseCustomerQuery)
            ->whereYear('created_at', $month->year)
            ->whereMonth('created_at', $month->month)
            ->count();
    });

    // ✅ RENEWALS per month
    $renewals = $months->map(function ($month) use ($baseCustomerQuery) {
        return (clone $baseCustomerQuery)
            ->whereYear('expiry_date', $month->year)
            ->whereMonth('expiry_date', $month->month)
            ->whereRaw('DATE(expiry_date) != DATE(created_at)')
            ->count();
    });

    // ✅ CHURN per month
    $churned = $months->map(function ($month) use ($baseCustomerQuery) {
        return (clone $baseCustomerQuery)
            ->whereDate('expiry_date', '<=', $month->endOfMonth())
            ->whereDoesntHave('payments')
            ->count();
    });

    // ✅ ACTIVE USERS (also company-scoped)
    $activeUsers = $months->map(function ($month) use ($user) {

        $snap = ExpirySnapshot::query()
            ->when(!$user->is_super_admin, fn ($q) =>
                $q->where('company_id', $user->company_id)
            )
            ->whereDate('snapshot_date', $month->format('Y-m-d'))
            ->first();

        return $snap->active_users ?? 0;
    });

    // ✅ RETENTION RATE
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
