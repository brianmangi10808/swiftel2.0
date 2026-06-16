<?php

namespace App\Filament\Resources\Packages\Pages;

use App\Filament\Resources\Packages\PackagesResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreatePackages extends CreateRecord
{
    protected static string $resource = PackagesResource::class;
    // app/Filament/Resources/Packages/Pages/CreatePackage.php
protected function mutateFormDataBeforeCreate(array $data): array
{
    $user = Auth::user();

    if (! $user?->is_super_admin) {
        $data['company_id'] = $user->company_id;
    }

    return $data;
}
}
