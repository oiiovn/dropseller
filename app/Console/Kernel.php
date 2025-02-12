<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('fetch:transactions')->everyMinutes();
        $schedule->command('order:process')->dailyAt('02:00'); // Chạy lúc 2h mỗi ngày
        $schedule->command('orders:auto-payment')->everyFiveMinutes(); //5 phút 1 lần
        $schedule->command('orders:update-reconciled')->dailyAt('02:00'); // Chạy lúc 2h sáng mỗi ngày
        // $schedule->command('auto:task')->daily(); // Chạy mỗi ngày
    }
    

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
    
    
}
