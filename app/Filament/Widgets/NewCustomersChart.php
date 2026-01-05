<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
class NewCustomersChart extends ChartWidget
{
    protected static ?string $heading = 'New Customers';
    protected static ?string $description = 'New customer registrations trend.';
    public ?string $filter = 'this_year';

    protected function getFilters(): ?array
    {
        return [
            'today'      => 'Today',
            'last_week'  => 'Last week',
            'this_week'  => 'This week',
            'last_month' => 'Last month',
            'this_month' => 'This month',
            'last_year'  => 'Last year',
            'this_year'  => 'This year',
        ];
    }

    protected function getData(): array
    {
        $user = Auth::user();
    // ✅ Multi-company filtering applied here
    $customers = Customer::query()
        ->when(!$user->is_super_admin, function ($q) use ($user) {
            return $q->where('company_id', $user->company_id);
        })
        ->orderBy('created_at')
        ->get()
        ->map(function ($customer) {
            $customer->date = Carbon::parse($customer->created_at);
            return $customer;
        });
        // Filter customers based on selected time range
        $customers = $customers->filter(function ($customer) {
            switch ($this->filter) {
                case 'today':
                    return $customer->date->isToday();

                case 'last_week':
                    return $customer->date->between(
                        Carbon::now()->subWeek()->startOfWeek(),
                        Carbon::now()->subWeek()->endOfWeek()
                    );

                case 'this_week':
                    return $customer->date->isCurrentWeek();

                case 'last_month':
                    return $customer->date->month == Carbon::now()->subMonth()->month &&
                           $customer->date->year == Carbon::now()->subMonth()->year;

                case 'this_month':
                    return $customer->date->month == Carbon::now()->month &&
                           $customer->date->year == Carbon::now()->year;

                case 'last_year':
                    return $customer->date->year == Carbon::now()->subYear()->year;

                default:
                case 'this_year':
                    return $customer->date->year == Carbon::now()->year;
            }
        });

        // Group data based on the filter type
        switch ($this->filter) {
            case 'today':
                return $this->getHourlyData($customers);

            case 'last_week':
            case 'this_week':
                return $this->getDailyData($customers);

            case 'last_month':
            case 'this_month':
                return $this->getDateData($customers);

            case 'last_year':
            case 'this_year':
            default:
                return $this->getMonthlyData($customers);
        }
    }

    /**
     * Group customers by hour (00-23)
     */
    protected function getHourlyData($customers): array
    {
        $hourlyCounts = array_fill(0, 24, 0);

        foreach ($customers as $customer) {
            $hour = $customer->date->hour;
            $hourlyCounts[$hour]++;
        }

        // Generate labels (00, 01, 02, ..., 23)
        $labels = array_map(fn($h) => str_pad($h, 2, '0', STR_PAD_LEFT), range(0, 23));

        return [
            'datasets' => [
                [
                    'label' => 'New Customers',
                    'data'  => array_values($hourlyCounts),
                    'backgroundColor' => '#3b82f6',
                    'borderColor' => '#2563eb',
                ]
            ],
            'labels' => $labels
        ];
    }

    /**
     * Group customers by day of week (Mon, Tue, Wed, ...)
     */
    protected function getDailyData($customers): array
    {
        // Determine the start of the week
        $startOfWeek = $this->filter === 'last_week' 
            ? Carbon::now()->subWeek()->startOfWeek() 
            : Carbon::now()->startOfWeek();

        $dailyCounts = [];
        $labels = [];

        // Create 7 days
        for ($i = 0; $i < 7; $i++) {
            $day = $startOfWeek->copy()->addDays($i);
            $labels[] = $day->format('D'); // Mon, Tue, Wed, etc.
            $dailyCounts[$i] = 0;
        }

        foreach ($customers as $customer) {
            $dayIndex = $customer->date->dayOfWeek; // 0 (Sunday) - 6 (Saturday)
            // Adjust to make Monday = 0
            $dayIndex = ($dayIndex + 6) % 7;
            $dailyCounts[$dayIndex]++;
        }

        return [
            'datasets' => [
                [
                    'label' => 'New Customers',
                    'data'  => array_values($dailyCounts),
                    'backgroundColor' => '#3b82f6',
                    'borderColor' => '#2563eb',
                ]
            ],
            'labels' => $labels
        ];
    }

    /**
     * Group customers by date (1, 2, 3, ..., 31)
     */
    protected function getDateData($customers): array
    {
        // Determine which month we're looking at
        $targetMonth = $this->filter === 'last_month'
            ? Carbon::now()->subMonth()
            : Carbon::now();

        $daysInMonth = $targetMonth->daysInMonth;
        $dateCounts = array_fill(1, $daysInMonth, 0);

        foreach ($customers as $customer) {
            $day = $customer->date->day;
            if ($day <= $daysInMonth) {
                $dateCounts[$day]++;
            }
        }

        // Generate labels (01, 02, 03, ..., 31)
        $labels = array_map(fn($d) => str_pad($d, 2, '0', STR_PAD_LEFT), range(1, $daysInMonth));

        return [
            'datasets' => [
                [
                    'label' => 'New Customers',
                    'data'  => array_values($dateCounts),
                    'backgroundColor' => '#3b82f6',
                    'borderColor' => '#2563eb',
                ]
            ],
            'labels' => $labels
        ];
    }

    /**
     * Group customers by month (Jan, Feb, Mar, ...)
     */
    protected function getMonthlyData($customers): array
    {
        $monthlyCounts = array_fill(1, 12, 0);

        foreach ($customers as $customer) {
            $month = $customer->date->month;
            $monthlyCounts[$month]++;
        }

        $labels = [
            'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
            'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'
        ];

        return [
            'datasets' => [
                [
                    'label' => 'New Customers',
                    'data'  => array_values($monthlyCounts),
                    'backgroundColor' => '#3b82f6',
                    'borderColor' => '#2563eb',
                ]
            ],
            'labels' => $labels
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}