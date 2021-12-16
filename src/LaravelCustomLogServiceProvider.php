<?php

namespace Computan\LaravelCustomLog;

use Computan\Jobs\SendEmailsJob;
use Illuminate\Support\Facades\Queue;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Debug\ExceptionHandler;

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
            $exception = [
                "name" => get_class($event->exception),
                "message" => $event->exception->getMessage(),
                "file" => $event->exception->getFile(),
                "line" => $event->exception->getLine(),
                "type"=>"job",
            ];
            dispatch(new SendEmailsJob($exception))->delay(5);
        });

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/config/custom-log.php' => config_path('custom-log.php')
            ], 'config');

            $this->publishes([

                __DIR__ . '/migrations/2021_12_13_000000_create_logs_table.php' => base_path('database/migrations/2021_12_13_000000s_create_logs_table.php')
            ], 'migration');

            $this->loadViewsFrom(__DIR__ . '/./resources/views', 'exceptions');
        }
    }
}
