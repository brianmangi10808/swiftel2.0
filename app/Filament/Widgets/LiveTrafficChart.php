<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class LiveTrafficChart extends Widget
{
    // The current record from the view page
    public ?\Illuminate\Database\Eloquent\Model $record = null;

    // The view file to render
    protected static string $view = 'filament.widgets.live-traffic-chart';

    // Helper method to get the PPPoE interface
    public function getPppoeInterface(): string
    {
        if (!$this->record || !$this->record->username) {
            return '';
        }
        
        return urlencode("<pppoe-{$this->record->username}>");
    }

    // Optional: Configure the widget size
    protected function getColumnSpan(): int|string|array
    {
        return 'full'; // Makes it take full width
    }
}