<?php
namespace App\Console\Commands;

use App\Models\Customer;
use App\Models\Setting;
use Illuminate\Console\Command;

class SendExpiryReminders extends Command
{
    protected $signature = 'pppoe:send-expiry-reminders';
    protected $description = 'Send PPPoE expiry reminder SMS based on configured reminder times';

    public function handle(): int
    {
        $settings = Setting::first();
        if (! $settings || empty($settings->pppoe_expiry_reminder_times)) {
            $this->info('No reminder times configured.');
            return self::SUCCESS;
        }

        $now = now();

        foreach ($settings->pppoe_expiry_reminder_times as $code) {
            [$from, $to] = $this->getWindowForOffset($code, $now);

            $this->info("Processing window {$code}: {$from} -> {$to}");

            Customer::whereNotNull('expiry_date')
                ->whereBetween('expiry_date', [$from, $to])
                ->chunkById(100, function ($customers) use ($code) {
                    foreach ($customers as $customer) {
                        // Optional: check Messages table so we don't double-send for same window
                        $alreadySent = \App\Models\Messages::where('recipient', $customer->mobile_number)
                            ->where('channel', 'like', 'expiry_%')
                            ->where('status', 'sent')
                            ->where('scheduled_at', '>=', now()->subDays(10))
                            ->exists();

                        if ($alreadySent) {
                            continue;
                        }

                        $ok = $customer->sendSmsFromTemplate('expiry');

                        if ($ok) {
                            // Flag this window in channel for debugging/reporting
                            \App\Services\SmsLogger::log(
                                recipient: $customer->mobile_number,
                                message: 'Expiry reminder window: '.$code,
                                channel: 'expiry_'.$code,
                                status: 'sent'
                            );
                        }
                    }
                });
        }

        return self::SUCCESS;
    }

    protected function getWindowForOffset(string $code, \Carbon\Carbon $now): array
    {
        // We use a small time window so the command can run every hour.
        // Adjust as you prefer.

        return match ($code) {
            '7_days_before'   => [$now->copy()->addDays(7)->startOfDay(),   $now->copy()->addDays(7)->endOfDay()],
            '5_days_before'   => [$now->copy()->addDays(5)->startOfDay(),   $now->copy()->addDays(5)->endOfDay()],
            '4_days_before'   => [$now->copy()->addDays(4)->startOfDay(),   $now->copy()->addDays(4)->endOfDay()],
            '3_days_before'   => [$now->copy()->addDays(3)->startOfDay(),   $now->copy()->addDays(3)->endOfDay()],
            '2_days_before'   => [$now->copy()->addDays(2)->startOfDay(),   $now->copy()->addDays(2)->endOfDay()],
            '1_day_before'    => [$now->copy()->addDay()->startOfDay(),     $now->copy()->addDay()->endOfDay()],
            '12_hours_before' => [$now->copy()->addHours(12)->subMinutes(30), $now->copy()->addHours(12)->addMinutes(30)],
            '6_hours_before'  => [$now->copy()->addHours(6)->subMinutes(30),  $now->copy()->addHours(6)->addMinutes(30)],
            '4_hours_before'  => [$now->copy()->addHours(4)->subMinutes(30),  $now->copy()->addHours(4)->addMinutes(30)],
            default           => [$now, $now],
        };
    }
}
