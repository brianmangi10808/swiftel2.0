<?php

namespace App\Filament\Resources\SmsGatewayResource\Pages;

use App\Filament\Resources\SmsGatewayResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Models\SmsGateway;

class EditSmsGateway extends EditRecord
{
    protected static string $resource = SmsGatewayResource::class;


    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
     protected function afterSave(): void
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
