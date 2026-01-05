<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    
    protected function schedule(Schedule $schedule)
    {
         $schedule->command('snapshot:expiry')->dailyAt('00:10');
             $schedule->command('customers:prune-expired')->dailyAt('01:00');

        // Check customer status every 10 minutes (optimized bulk check)
        $schedule->command('customers:check-status')
                ->everyMinute()
                 ->withoutOverlapping()
                 ->runInBackground();
                 $schedule->command('pppoe:send-expiry-reminders')->hourly();
                  $schedule->call(function () {
        $setting = \App\Models\Setting::first();
        $windows = $setting->reminderWindows();

        foreach ($windows as $key => [$from, $to]) {
            \App\Models\Customer::whereBetween('expiry_date', [$from, $to])
                ->get()
                ->each(function ($customer) use ($key) {
                    $customer->sendSmsFromTemplate('expiry');
                });
        }
    })->everyMinute();
    }



    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}