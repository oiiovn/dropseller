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
        $schedule->command('fetch:transactions')->everyMinute();
        $schedule->command('order:process')->dailyAt('02:00'); // Chạy lúc 2h mỗi ngày
        $schedule->command('orders:auto-payment')->everyFiveMinutes(); //5 phút 1 lần
        $schedule->command('orders:update-reconciled')->dailyAt('02:00'); // Chạy lúc 2h sáng mỗi ngày
        $schedule->command('ads:auto-payment')->everyFiveMinutes();
        $schedule->command('PRG:auto-payment-progarm')->everyMinute();
        $schedule->command('balance:rebuild-all')->dailyAt('01:00');
        $schedule->command('auto:settle-monthly')
            ->monthlyOn(24, '00:05') // chạy vào 00:05 ngày 24 hàng tháng
            ->withoutOverlapping();
        // $schedule->command('check:balance-ai')->dailyAt('01:00');
    }


    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
    protected $commands = [
        \App\Console\Commands\AutoPaymentAds::class,
    ];
}
