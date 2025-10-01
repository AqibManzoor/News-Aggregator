<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\FetchNewsCommand;

class Kernel extends ConsoleKernel
{
    /**
     * Explicitly registered Artisan commands to avoid directory scanning.
     *
     * @var array<int, class-string>
     */
    protected $commands = [
        FetchNewsCommand::class,
    ];
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('news:fetch')->hourly();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        // Keep directory scan disabled; property-based registration is used.
        // $this->load(__DIR__.'/Commands');
        // require base_path('routes/console.php');
    }
}
