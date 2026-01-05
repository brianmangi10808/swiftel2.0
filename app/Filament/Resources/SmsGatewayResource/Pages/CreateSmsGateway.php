<?php

namespace App\Filament\Resources\SmsGatewayResource\Pages;

use App\Filament\Resources\SmsGatewayResource;
use App\Models\SmsGateway;

use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateSmsGateway extends CreateRecord
{
    protected static string $resource = SmsGatewayResource::class;
  protected function afterCreate(): void
    {
        $this->syncActiveGateway();
    }

    protected function syncActiveGateway(): void
    {
        if (! $this->record->is_active) {
            return;
        }

        SmsGateway::where('company_id', $this->record->company_id)
            ->where('id', '!=', $this->record->id)
            ->update(['is_active' => false]);
    }

}
