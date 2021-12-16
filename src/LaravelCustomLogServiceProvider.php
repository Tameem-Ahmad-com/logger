<?php

namespace Computan\LaravelCustomLog;


use Illuminate\Support\Facades\Queue;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Support\ServiceProvider;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Computan\LaravelCustomLog\Commands\SendErrorEmailCommand;
use Computan\LaravelCustomLog\Jobs\SendExceptionEmailJob;

class LaravelCustomLogServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
    }

    public function boot()
    {
      
        /* Binding package exception into laravel ExceptionHandler interface*/
        $this->app->bind(
            ExceptionHandler::class,
            Handler::class
        );
        /* getting fialed job exception */
        Queue::failing(function (JobFailed $event) {
            Notifications::error('laravel', 'job', collect($event->exception)->toArray());
        });

        if ($this->app->runningInConsole()) {
            // $this->commands([
            //    SendErrorEmailCommand::class,
               
            // ]);
      
            $this->publishes([
                __DIR__ . '/config/custom-log.php' => config_path('custom-log.php')
            ], 'config');

            $this->publishes([

                __DIR__ . '/migrations/2021_12_13_000000_create_logs_table.php' => base_path('database/migrations/2021_12_13_000000s_create_logs_table.php')
            ], 'migration');

            $this->app->booted(function () {
                $schedule = $this->app->make(Schedule::class);
                $schedule->job(new SendExceptionEmailJob())->everyMinute();
            });
        }
    }
}
