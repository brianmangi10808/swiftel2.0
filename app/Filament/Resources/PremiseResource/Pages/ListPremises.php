<?php

namespace App\Filament\Resources\PremiseResource\Pages;

use App\Filament\Resources\PremiseResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPremises extends ListRecords
{
    protected static string $resource = PremiseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
