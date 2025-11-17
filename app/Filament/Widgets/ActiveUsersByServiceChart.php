<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Customer;
use App\Models\Service;

class ActiveUsersByServiceChart extends ChartWidget
{
    protected static ?string $heading = 'Active Users by Service';
    protected static ?string $description = 'Distribution of active customers across services.';

    // Remove the chartHeight property - let it use default height
    // This will match the height of bar charts and line charts

    public function getColumnSpan(): int|string|array
    {
        return 'half'; // Takes up half the width
    }

    protected function getData(): array
    {
        $services = Service::all();

        return [
            'labels' => $services->pluck('name')->toArray(),
            'datasets' => [
                [
                    'label' => 'Active Users',
                    'data' => $services->map(function ($s) {
                        return Customer::where('service_id', $s->id)
                            ->where('expiry_date', '>=', now())
                            ->count();
                    })->toArray(),
                    'backgroundColor' => [
                        '#3b82f6', // blue
                        '#22c55e', // green
                        '#f59e0b', // amber
                        '#ef4444', // red
                        '#8b5cf6', // violet
                        '#ec4899', // pink
                        '#14b8a6', // teal
                        '#f97316', // orange
                        '#06b6d4', // cyan
                        '#6366f1', // indigo
                    ],
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'right',
                ],
            ],
            'maintainAspectRatio' => true,
            'aspectRatio' => 2, // Adjust this to control height (lower = taller)
        ];
    }
}