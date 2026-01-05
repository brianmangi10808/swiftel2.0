<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Customer;
use App\Models\ExpirySnapshot;
use App\Models\Company;
use Carbon\Carbon;

class CaptureExpirySnapshot extends Command
{
    protected $signature = 'snapshot:expiry';
    protected $description = 'Capture daily expiry and renewal snapshot PER COMPANY';

    public function handle()
    {
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();

        // ✅ LOOP THROUGH EACH COMPANY
        foreach (Company::all() as $company) {

            $companyId = $company->id;

            // Count users expired today
            $newExpiriesToday = Customer::where('company_id', $companyId)
                ->whereDate('expiry_date', $today)
                ->count();

            // Expired yesterday
            $expiredYesterday = Customer::where('company_id', $companyId)
                ->where('expiry_date', '<', $today)
                ->where('expiry_date', '>=', $yesterday)
                ->count();

            // Renewed today
            $renewedToday = Customer::where('company_id', $companyId)
                ->where('expiry_date', '>=', $today)
                ->where('updated_at', '>=', $today)
                ->whereIn('id', function ($query) use ($yesterday, $companyId) {
                    $query->select('id')
                        ->from('customers')
                        ->where('company_id', $companyId)
                        ->where('expiry_date', '<', now()->startOfDay())
                        ->whereDate('updated_at', $yesterday);
                })
                ->count();

            // Currently expired
            $currentlyExpired = Customer::where('company_id', $companyId)
                ->where('expiry_date', '<', $today)
                ->count();

            // Active users
            $activeUsers = Customer::where('company_id', $companyId)
                ->where('expiry_date', '>=', $today)
                ->count();

            // ✅ SAVE PER COMPANY SNAPSHOT
            ExpirySnapshot::create([
                'company_id'          => $companyId,
                'snapshot_date'       => $today,
                'expired_yesterday'   => $expiredYesterday,
                'currently_expired'   => $currentlyExpired,
                'renewed_today'       => $renewedToday,
                'new_expiries_today'  => $newExpiriesToday,
                'active_users'        => $activeUsers,
            ]);

            $this->info("✅ Snapshot saved for Company ID: {$companyId}");
        }

        $this->info("✅✅ All company snapshots captured successfully.");
    }
}
