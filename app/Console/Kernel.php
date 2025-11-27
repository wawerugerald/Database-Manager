<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\StartDatabase::class,
        \App\Console\Commands\StopDatabase::class,
        
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $interval = config('dbmanager.status_check_interval', 60);

        // Default: check every minute
        $schedule->command('dbmanager:check-status')->everyMinute();

        // For custom interval using config
        // $schedule->command('dbmanager:check-status')->cron("*/{$interval} * * * *");
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
