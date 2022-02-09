<?php

namespace Notify\LaravelCustomLog;


use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Route;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Support\ServiceProvider;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Notify\LaravelCustomLog\Mail\ExceptionEmail;
use Notify\LaravelCustomLog\Jobs\SendReportEmailJob;
use Notify\LaravelCustomLog\Jobs\SendExceptionEmailJob;


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

        if (config('custom-log.custom_log_mysql_enable')) {

            /* Binding package exception into laravel ExceptionHandler interface*/
            $this->app->bind(
                ExceptionHandler::class,
                Handler::class
            );
            /* getting fialed job exception */
            Queue::failing(function (JobFailed $event) {
                Notifications::error('job', $event->exception->getMessage(), $event->exception->getTrace());
            });
        }
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/config/custom-log.php' => config_path('custom-log.php')
            ], 'config');

            $this->publishes([

                __DIR__ . '/migrations/2021_12_13_000000_create_logs_table.php' => base_path('database/migrations/2021_12_13_000000s_create_logs_table.php')
            ], 'migration');

            /* commands section */
            $this->app->booted(function () {
                $this->sendEmailReport();
                $this->sendEmailsToDeveloper();
            });
        }
        $this->loadRoutesFrom(__DIR__ . '/routes/web.php');
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'CustomLog');
    }

    protected function sendEmailsToDeveloper()
    {
        if (config('custom-log.dev-mode')) {
            if (Notifications::getDailyCount() > 0) {
                $schedule = $this->app->make(Schedule::class);
                $schedule->job(function () {
                    $errors = DB::table(config('custom-log.mysql.table'))->where('is_email_sent', 0)->get();
                    if (!empty($errors)) {
                        foreach ($errors as $error) {
                            Mail::to(config('custom-log.dev-emails'))->send(new ExceptionEmail($error));
                            $record = DB::table(config('custom-log.mysql.table'))->find($error->id);
                            if (!is_null($record)) {
                                DB::table(config('custom-log.mysql.table'))->where('id', $error->id)->update([
                                    'is_email_sent' => 1
                                ]);
                            }
                        }
                    }
                })->everyMinute();
                // $schedule->job(new SendExceptionEmailJob())->everyMinute();
            }
        }
    }
    protected function sendEmailReport()
    {
        if (Notifications::getDailyCount() > 0) {
            $schedule = $this->app->make(Schedule::class);
            if (!empty(config('custom-log.command'))) {
                $schedule->job(new SendReportEmailJob())->cron(config('custom-log.command'));
            } else {
                $schedule->job(new SendReportEmailJob())->daily();
            }
        }
    }
}
