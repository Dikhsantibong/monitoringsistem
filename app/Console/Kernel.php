<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * These should be classes that implement the ShouldQueue interface.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('commitments:check-overdue')->daily();
         // Jalankan pengecekan setiap hari jam 00:01
         $schedule->command('discussions:check-overdue')->dailyAt('00:01');
        $schedule->command('logs:clear')->daily();
        $schedule->call(function () {
            $controller = new \App\Http\Controllers\Admin\LaporanController();
            $result = $controller->checkAndMoveToBacklog();
            
            \Log::info('Scheduled backlog check completed', $result);
        })->hourly();
    }

    /**
     * Register the commands for your application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
} 