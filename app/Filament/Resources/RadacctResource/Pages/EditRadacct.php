<?php

namespace App\Filament\Resources\RadacctResource\Pages;

use App\Filament\Resources\RadacctResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRadacct extends EditRecord
{
    protected static string $resource = RadacctResource::class;

    protected function getHeaderActions(): array
    {
        return [
           // Actions\DeleteAction::make(),
        ];
    }
}
