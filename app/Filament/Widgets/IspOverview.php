<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Customer;
use App\Models\Extension;
use Illuminate\Support\Facades\Auth;


class IspOverview extends BaseWidget
{

        public static function canView(): bool
    {
        return Auth::user()->is_super_admin 
            || Auth::user()->can('read customers');
    }

        protected static ?string $pollingInterval = null;
public function getViewData(): array
    {
        return array_merge(parent::getViewData(), [
            'columns' => 5,
        ]);
    }
    protected function getStats(): array
    {
         // Base query — always scoped to the current user's company
        $query = Customer::query();
           $extensionQuery = Extension::query();

        $user = Auth::user();

        // Super admin sees ALL companies
       if (!$user?->is_super_admin) {
            $query->where('company_id', $user->company_id);
            $extensionQuery->whereHas('customer', function ($q) use ($user) {
                $q->where('company_id', $user->company_id);
            });
        }

       $total   = (clone $query)->count();
        $online  = (clone $query)->where('status', 'online') ->whereNotNull('expiry_date') ->where('expiry_date', '>=', now())->count();
        $offline = (clone $query) ->where('status', 'offline') ->whereNotNull('expiry_date') ->where('expiry_date', '>=', now()) ->count();
        $expired = (clone $query)->where('expiry_date', '<', now())->count();


        return [
            Stat::make('Total Customers', $total)
                ->color('primary')
                ->description('All registered clients'),

            Stat::make('Online Users', $online)
                ->color('success')
                ->description('Active PPPoE sessions'),

            Stat::make('Offline Users', $offline)
                ->color('danger')
                ->description('Currently not connected'),

            Stat::make('Expired Accounts', $expired)
                ->color('warning')
                ->description('Needs renewal'),
              
            Stat::make('Extensions This Month', 
                $extensionQuery->whereMonth('created_at', now()->month)->count()
            )
            ->description('Extensions in ' . now()->format('F'))
            ->icon('heroicon-o-calendar')
            ->color('info'),    
        ];
    }
}
