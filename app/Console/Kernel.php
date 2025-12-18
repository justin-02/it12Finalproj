<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {
        // Idle detection every 5 minutes
        $schedule->command('employees:detect-idle')->everyFiveMinutes();

        // Retention purge daily
        $schedule->command('employees:purge-logs')->daily();

        // Productivity report generation daily at 1:00
        $schedule->command('employees:generate-daily-report')->dailyAt('01:00');
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
