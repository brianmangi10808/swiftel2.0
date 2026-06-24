<?php

namespace App\Filament\Resources\Clients\Pages;

use App\Filament\Resources\Clients\ClientsResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
class CreateClients extends CreateRecord
{
    protected static string $resource = ClientsResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
{
    $user = Auth::user();

    if (! $user?->is_super_admin) {
        $data['company_id'] = $user->company_id;
    }

    return $data;
}
}
