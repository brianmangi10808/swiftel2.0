<?php

namespace App\Filament\Resources\Customers\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class CustomerDataUsageChart extends ChartWidget
{
    protected ?string $heading = 'Data Usage';

    protected int|string|array $columnSpan = 'full';

    public ?Model $record = null;

    protected function getData(): array
    {
        $labels   = [];
        $download = [];
        $upload   = [];

        for ($i = 6; $i >= 0; $i--) {
            $day = Carbon::today()->subDays($i);

            $sessions = $this->record
    ->sessions()
    ->whereDate('acctstarttime', $day)
    ->get(['acctinputoctets', 'acctoutputoctets']);

            // bytes → GB
            $upload[]   = round($sessions->sum('acctinputoctets') / 1_073_741_824, 2);
            $download[] = round($sessions->sum('acctoutputoctets') / 1_073_741_824, 2);

            $labels[] = $day->format('M j');
        }

        return [
            'datasets' => [
                [
                    'label'           => 'Download (GB)',
                    'data'            => $download,
                    'borderColor'     => 'rgb(59, 130, 246)',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.15)',
                    'fill'            => true,
                    'tension'         => 0.4,
                ],
                [
                    'label'           => 'Upload (GB)',
                    'data'            => $upload,
                    'borderColor'     => 'rgb(249, 115, 22)',
                    'backgroundColor' => 'rgba(249, 115, 22, 0.15)',
                    'fill'            => true,
                    'tension'         => 0.4,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}