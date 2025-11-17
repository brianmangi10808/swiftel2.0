<?php

namespace App\Filament\Resources\RadacctResource\Pages;

use App\Filament\Resources\RadacctResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRadaccts extends ListRecords
{
    protected static string $resource = RadacctResource::class;

    protected function getHeaderActions(): array
    {
        return [
           // Actions\CreateAction::make(),
        ];
    }
}
