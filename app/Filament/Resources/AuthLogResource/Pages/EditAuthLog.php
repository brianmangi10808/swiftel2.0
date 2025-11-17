<?php

namespace App\Filament\Resources\AuthLogResource\Pages;

use App\Filament\Resources\AuthLogResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAuthLog extends EditRecord
{
    protected static string $resource = AuthLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
