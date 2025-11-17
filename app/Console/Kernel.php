<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {
         $schedule->command('snapshot:expiry')->dailyAt('00:10');
        // Check customer status every 10 minutes (optimized bulk check)
        $schedule->command('customers:check-status')
                ->everyMinute()
                 ->withoutOverlapping()
                 ->runInBackground();
    }



    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}