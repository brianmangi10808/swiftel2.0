<?php

namespace App\Filament\Resources\PremiseResource\Pages;

use App\Filament\Resources\PremiseResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPremise extends EditRecord
{
    protected static string $resource = PremiseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
