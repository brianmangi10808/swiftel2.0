<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Customer;
use App\Models\SystemSetting;
use Carbon\Carbon;

class PruneExpiredCustomers extends Command
{
    protected $signature = 'customers:prune-expired';
    protected $description = 'Prune customers after expiry and send prune notices based on system settings';

    public function handle()
    {
        // 1. Fetch prune duration from system settings
        $setting = SystemSetting::get('prune_after_expiry', '30_days');

        $daysMapping = [
            '7_days'   => 7,
            '14_days'  => 14,
            '30_days'  => 30,
            '60_days'  => 60,
            '90_days'  => 90,
            '180_days' => 180,
            '1_year'   => 365,
        ];

        $days = $daysMapping[$setting] ?? 30;

        // 2. Calculate the cutoff date
        $cutoff = Carbon::now()->subDays($days);

        // 3. Fetch customers to prune
        $customers = Customer::whereNotNull('expiry_date')
            ->where('expiry_date', '<=', $cutoff)
            ->where('enable', true)
            ->get();

        $this->info("Found {$customers->count()} customers eligible for pruning...");

        // Counters
        $pruned = 0;
        $smsSent = 0;

        // 4. Process each customer
        foreach ($customers as $customer) {

            // 4A. Send prune notice SMS
            try {
                if ($customer->sendSmsFromTemplate('prune_notice')) {
                    $smsSent++;
                }
            } catch (\Throwable $e) {
               
               
            }

            // 4B. Disable & mark the customer as pruned
            $customer->update([
                'enable' => false,
                'status' => 'pruned',
            ]);

            $pruned++;
        }

        // 5. Output summary
        $this->info("Pruned {$pruned} customers.");
        $this->info("Prune notices sent: {$smsSent}");

        return Command::SUCCESS;
    }
}
