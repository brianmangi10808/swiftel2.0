<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Customer;
use App\Models\Service;
use Illuminate\Support\Facades\Auth;

class ActiveUsersByServiceChart extends ChartWidget
{
    protected static ?string $heading = 'Active Users by Service';
    protected static ?string $description = 'Distribution of active customers across services.';

    public function getColumnSpan(): int|string|array
    {
        return 'half';
    }
 public static function canView(): bool
    {
        return Auth::user()->is_super_admin 
            || Auth::user()->can('read customers');
    }
    protected function getData(): array
    {
        $user = Auth::user();

      
        $services = $user->is_super_admin
            ? Service::all()
            : Service::where('company_id', $user->company_id)->get();

        return [
            'labels' => $services->pluck('name')->toArray(),
            'datasets' => [
                [
                    'label' => 'Active Users',
                    'data' => $services->map(function ($service) use ($user) {

                        $query = Customer::where('service_id', $service->id)
                            ->where('expiry_date', '>=', now());

                        
                        if (! $user->is_super_admin) {
                            $query->where('company_id', $user->company_id);
                        }

                        return $query->count();

                    })->toArray(),

                    'backgroundColor' => [
                        '#3b82f6',
                        '#22c55e',
                        '#f59e0b',
                        '#ef4444',
                        '#8b5cf6',
                        '#ec4899',
                        '#14b8a6',
                        '#f97316',
                        '#06b6d4',
                        '#6366f1',
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
            'aspectRatio' => 2,
        ];
    }
}
