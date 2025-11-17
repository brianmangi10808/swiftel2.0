<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Customer;
use App\Models\ExpirySnapshot;
use Carbon\Carbon;

class CaptureExpirySnapshot extends Command
{
    protected $signature = 'snapshot:expiry';
    protected $description = 'Capture daily expiry and renewal snapshot';

    public function handle()
    {
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();

        // Snapshot from yesterday (if exists)
        $yesterdaySnapshot = ExpirySnapshot::whereDate('snapshot_date', $yesterday)->first();

        // Count users expired today
        $newExpiriesToday = Customer::whereDate('expiry_date', $today)->count();

        // Expired yesterday (actual customer data)
        $expiredYesterday = Customer::where('expiry_date', '<', $today)
            ->where('expiry_date', '>=', $yesterday)
            ->count();

        // Detect renewals:
        // A renewal = user expired yesterday BUT today expiry_date is in future
        $renewedToday = Customer::where('expiry_date', '>=', $today)
            ->where('updated_at', '>=', $today) // renewed today
            ->whereIn('id', function ($query) use ($yesterday) {
                $query->select('id')
                      ->from('customers')
                      ->where('expiry_date', '<', now()->startOfDay()) // they were expired before today
                      ->whereDate('updated_at', $yesterday);           // updated yesterday = renewal processed
            })
            ->count();

        // Currently expired now
        $currentlyExpired = Customer::where('expiry_date', '<', $today)->count();

        // Active today
        $activeUsers = Customer::where('expiry_date', '>=', $today)->count();

        ExpirySnapshot::create([
            'snapshot_date'       => $today,
            'expired_yesterday'   => $expiredYesterday,
            'currently_expired'   => $currentlyExpired,
            'renewed_today'       => $renewedToday,
            'new_expiries_today'  => $newExpiriesToday,
            'active_users'        => $activeUsers,
        ]);

        $this->info("✅ Daily expiry snapshot saved.");
    }
}
