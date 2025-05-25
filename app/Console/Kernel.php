<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected $commands = [
       Commands\SendEmailInvoiceCommand::class,
       Commands\DemoCron::class,
       Commands\CronJobRemove::class,
    ];

    protected function schedule(Schedule $schedule)
    {
        Log::channel('stack')->info('Scheduler running at: ' . now()->toDateTimeString());

        $schedule->command('send:birthday-wishes')->daily()->appendOutputTo(storage_path('logs/scheduler.log'))
            ->before(function () {
                Log::channel('stack')->info('About to run birthday wishes command', [
                    'time' => now()->toDateTimeString(),
                    'timezone' => config('app.timezone')
                ]);
            })
            ->after(function () {
                Log::channel('stack')->info('Completed birthday wishes command');
            })
            ->onFailure(function () {
                Log::channel('stack')->error('Failed to run birthday wishes command');
            });


        if (Cache::get('invoice-cron-enabled', true)) {
          $repeat = Cache::get('invoice-cron-repeat');
          if($repeat == 'montly') $repeat = 'monthly';
            // $client_id = 103;
            // $schedule->command('email:send-invoice ' . $client_id)
            //          ->{$repeat}()
            //          ->withoutOverlapping();
        }

        $schedule->command('staff:check-activity')->hourly()->appendOutputTo(storage_path('logs/scheduler.log'));
        $schedule->command('report:client-summary')->everyMinute(1, '08:00')->appendOutputTo(storage_path('logs/client-reports.log'));
        $schedule->command('reports:generate-monthly-staff')->everyMinute(1, '02:00')->appendOutputTo(storage_path('logs/staff-reports.log'));
        // $schedule->command('report:client-summary')->lastDayOfMonth('23:00')->appendOutputTo(storage_path('logs/client-reports.log'));
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
