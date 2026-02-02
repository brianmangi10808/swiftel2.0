<?php

namespace App\Filament\Resources\CustomerResource\Pages;

use App\Filament\Resources\CustomerResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Jobs\NotifyMikrotikBackend;

class EditCustomer extends EditRecord
{
    protected static string $resource = CustomerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            
              Actions\DeleteAction::make(),
       
        Actions\RestoreAction::make(),
        ];
        
    }
   protected function afterSave(): void
    {
        // Dispatch job to notify backend with just the customer ID
        NotifyMikrotikBackend::dispatch($this->record->id);
    }
}
