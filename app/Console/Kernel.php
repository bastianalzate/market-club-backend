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
        // Procesar renovaciones automáticas diariamente a las 3:00 PM (para pruebas)
        $schedule->command('subscriptions:process-renewals')
                 ->dailyAt('15:00')
                 ->withoutOverlapping()
                 ->runInBackground();

        // Notificar vencimientos 7 días antes
        $schedule->command('subscriptions:notify-expirations --days=7')
                 ->dailyAt('09:00')
                 ->withoutOverlapping();

        // Notificar vencimientos 3 días antes
        $schedule->command('subscriptions:notify-expirations --days=3')
                 ->dailyAt('09:30')
                 ->withoutOverlapping();

        // Notificar vencimientos 1 día antes
        $schedule->command('subscriptions:notify-expirations --days=1')
                 ->dailyAt('10:00')
                 ->withoutOverlapping();

        // Limpiar logs antiguos semanalmente
        $schedule->command('log:clear')
                 ->weekly()
                 ->sundays()
                 ->at('02:00');
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
