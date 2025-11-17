<?php

namespace App\Filament\Resources\AuthLogResource\Pages;

use App\Filament\Resources\AuthLogResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateAuthLog extends CreateRecord
{
    protected static string $resource = AuthLogResource::class;
}
