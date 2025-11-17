<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Payment;
use Carbon\Carbon;

class PaymentsChart extends ChartWidget
{
    protected static ?string $heading = 'Payments';
    protected static ?string $description = 'Payments and expenses trend.';
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
        // Get all payments and convert trans_time to Carbon instances
        $payments = Payment::query()
            ->orderBy('trans_time')
            ->get()
            ->map(function ($p) {
                // Convert 20250102153035 → Carbon instance
                $p->date = Carbon::createFromFormat('YmdHis', $p->trans_time);
                return $p;
            });

        // Filter payments based on selected time range
        $payments = $payments->filter(function ($p) {
            switch ($this->filter) {
                case 'today':
                    return $p->date->isToday();

                case 'last_week':
                    return $p->date->between(
                        Carbon::now()->subWeek()->startOfWeek(),
                        Carbon::now()->subWeek()->endOfWeek()
                    );

                case 'this_week':
                    return $p->date->isCurrentWeek();

                case 'last_month':
                    return $p->date->month == Carbon::now()->subMonth()->month &&
                           $p->date->year == Carbon::now()->subMonth()->year;

                case 'this_month':
                    return $p->date->month == Carbon::now()->month &&
                           $p->date->year == Carbon::now()->year;

                case 'last_year':
                    return $p->date->year == Carbon::now()->subYear()->year;

                default:
                case 'this_year':
                    return $p->date->year == Carbon::now()->year;
            }
        });

        // Group data based on the filter type
        switch ($this->filter) {
            case 'today':
                return $this->getHourlyData($payments);

            case 'last_week':
            case 'this_week':
                return $this->getDailyData($payments);

            case 'last_month':
            case 'this_month':
                return $this->getDateData($payments);

            case 'last_year':
            case 'this_year':
            default:
                return $this->getMonthlyData($payments);
        }
    }

    /**
     * Group payments by hour (00-23)
     */
    protected function getHourlyData($payments): array
    {
        $hourlyTotals = array_fill(0, 24, 0);

        foreach ($payments as $p) {
            $hour = $p->date->hour;
            $hourlyTotals[$hour] += (int) $p->trans_amount;
        }

        // Generate labels (00, 01, 02, ..., 23)
        $labels = array_map(fn($h) => str_pad($h, 2, '0', STR_PAD_LEFT), range(0, 23));

        return [
            'datasets' => [
                [
                    'label' => 'Payments',
                    'data'  => array_values($hourlyTotals),
                    'backgroundColor' => '#22c55e',
                ]
            ],
            'labels' => $labels
        ];
    }

    /**
     * Group payments by day of week (Mon, Tue, Wed, ...)
     */
    protected function getDailyData($payments): array
    {
        // Determine the start of the week
        $startOfWeek = $this->filter === 'last_week' 
            ? Carbon::now()->subWeek()->startOfWeek() 
            : Carbon::now()->startOfWeek();

        $dailyTotals = [];
        $labels = [];

        // Create 7 days
        for ($i = 0; $i < 7; $i++) {
            $day = $startOfWeek->copy()->addDays($i);
            $labels[] = $day->format('D'); // Mon, Tue, Wed, etc.
            $dailyTotals[$i] = 0;
        }

        foreach ($payments as $p) {
            $dayIndex = $p->date->dayOfWeek; // 0 (Sunday) - 6 (Saturday)
            // Adjust to make Monday = 0
            $dayIndex = ($dayIndex + 6) % 7;
            $dailyTotals[$dayIndex] += (int) $p->trans_amount;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Payments',
                    'data'  => array_values($dailyTotals),
                    'backgroundColor' => '#22c55e',
                ]
            ],
            'labels' => $labels
        ];
    }

    /**
     * Group payments by date (1, 2, 3, ..., 31)
     */
    protected function getDateData($payments): array
    {
        // Determine which month we're looking at
        $targetMonth = $this->filter === 'last_month'
            ? Carbon::now()->subMonth()
            : Carbon::now();

        $daysInMonth = $targetMonth->daysInMonth;
        $dateTotals = array_fill(1, $daysInMonth, 0);

        foreach ($payments as $p) {
            $day = $p->date->day;
            if ($day <= $daysInMonth) {
                $dateTotals[$day] += (int) $p->trans_amount;
            }
        }

        // Generate labels (01, 02, 03, ..., 31)
        $labels = array_map(fn($d) => str_pad($d, 2, '0', STR_PAD_LEFT), range(1, $daysInMonth));

        return [
            'datasets' => [
                [
                    'label' => 'Payments',
                    'data'  => array_values($dateTotals),
                    'backgroundColor' => '#22c55e',
                ]
            ],
            'labels' => $labels
        ];
    }

    /**
     * Group payments by month (Jan, Feb, Mar, ...)
     */
    protected function getMonthlyData($payments): array
    {
        $monthlyTotals = array_fill(1, 12, 0);

        foreach ($payments as $p) {
            $month = $p->date->month;
            $monthlyTotals[$month] += (int) $p->trans_amount;
        }

        $labels = [
            'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
            'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'
        ];

        return [
            'datasets' => [
                [
                    'label' => 'Payments',
                    'data'  => array_values($monthlyTotals),
                    'backgroundColor' => '#22c55e',
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