<?php

namespace App\Filament\Resources\CustomerResource\Pages;

use App\Filament\Resources\CustomerResource;
use App\Models\Customer;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ListCustomers extends ListRecords
{
    protected static string $resource = CustomerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        $user = Auth::user();

        // ✅ Base query: scoped per company for normal users, global for super admin
        $baseQuery = Customer::query();

        if (! $user?->is_super_admin) {
            $baseQuery->where('company_id', $user->company_id);
        }

        return [
            'all' => Tab::make('All')
                ->icon('heroicon-o-users')
                // ✅ Use Filament's base query (already scoped by CustomerResource + this tab)
                ->modifyQueryUsing(fn (Builder $query) => $query)
                // ✅ Badge: use company-scoped base query
                ->badge((clone $baseQuery)->count()),

            'online' => Tab::make('Online')
                ->icon('heroicon-o-signal')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'online'))
                ->badge(
                    (clone $baseQuery)
                        ->where('status', 'online')
                        ->count()
                ),

            'offline' => Tab::make('Offline')
                ->icon('heroicon-o-signal-slash')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'offline'))
                ->badge(
                    (clone $baseQuery)
                        ->where('status', 'offline')
                        ->count()
                ),

            'enabled' => Tab::make('Enabled')
                ->icon('heroicon-o-check-circle')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('enable', true))
                ->badge(
                    (clone $baseQuery)
                        ->where('enable', true)
                        ->count()
                ),

            'disabled' => Tab::make('Disabled')
                ->icon('heroicon-o-x-circle')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('enable', false))
                ->badge(
                    (clone $baseQuery)
                        ->where('enable', false)
                        ->count()
                ),

            'expiring_24h' => Tab::make('Expiring ≤ 24h')
                ->icon('heroicon-o-clock')
                ->modifyQueryUsing(function (Builder $query) {
                    return $query
                        ->whereNotNull('expiry_date')
                        ->whereBetween('expiry_date', [now(), now()->addDay()]);
                })
                ->badge(
                    (clone $baseQuery)
                        ->whereNotNull('expiry_date')
                        ->whereBetween('expiry_date', [now(), now()->addDay()])
                        ->count()
                ),
        ];
    }

    public function getDefaultActiveTab(): string|int|null
    {
        return 'all';
    }
}
