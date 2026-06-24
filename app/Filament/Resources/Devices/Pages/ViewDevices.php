<?php

namespace App\Filament\Resources\Devices\Pages;

use App\Filament\Resources\Devices\DeviceResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewDevices extends ViewRecord
{
    protected static string $resource = DeviceResource::class;

    
    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}