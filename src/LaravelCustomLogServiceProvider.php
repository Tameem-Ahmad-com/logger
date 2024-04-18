<?php

namespace Notify\LaravelCustomLog;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Support\ServiceProvider;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Notify\LaravelCustomLog\Mail\ExceptionEmail;
use Notify\LaravelCustomLog\Mail\ReportEmail;
use Illuminate\Support\Facades\Log;

class LaravelCustomLogServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        // This space intentionally left blank.
    }

    public function boot()
    {
        try {
            // Register exception handling
            if (config('custom-log.custom_log_mysql_enable')) {
               
                // Bind package exception handler if configured
                if (config('custom-log.override_exception_handler')) {
                    $this->app->bind(ExceptionHandler::class, Handler::class);
                }

                // Listen for failed job events and log them
                Queue::failing(function (JobFailed $event) {
                    Notifications::error('job', $event->exception->getMessage(), $event->exception->getTrace());
                });
            }

            // Perform actions only in console environment
            if ($this->app->runningInConsole()) {
               
                // Publish required files
                $this->publishRequiredFiles();
               
                // Schedule tasks for sending reports and developer emails
                $this->app->booted(function () {
                 
                    if (config('custom-log.custom_log_mysql_enable')) {
                        
                        $this->sendEmailReport();
                        $this->sendEmailsToDeveloper();
                    }
                });
            }

            // Load routes and views
            $this->loadRoutesFrom(__DIR__ . '/routes/web.php');
            $this->loadViewsFrom(__DIR__ . '/resources/views', 'CustomLog');
        } catch (Exception $e) {
            // Log any boot-time exceptions
            Log::alert($e->getMessage());
        }
    }

    // Sends error emails to developers based on configured conditions
    protected function sendEmailsToDeveloper()
    {
        try {
           
            if (config('custom-log.dev-mode')) {
                if (Notifications::getDailyCount() > 0) {
                  
                    $schedule = $this->app->make(Schedule::class);
                    $schedule->call(function () {
                        $errors = DB::table(config('custom-log.mysql_table','logs'))->where('is_email_sent', 0)->get();
                       
                        if (!empty($errors)) {
                            foreach ($errors as $error) {
                                Mail::to(config('custom-log.dev-emails'))->send(new ExceptionEmail($error));
                                $record = DB::table(config('custom-log.mysql_table','logs'))->find($error->id);
                                if (!is_null($record)) {
                                    DB::table(config('custom-log.mysql_table','logs'))->where('id', $error->id)->update([
                                        'is_email_sent' => 1
                                    ]);
                                }
                            }
                        }
                    })->everyMinute();
                }
            }
        } catch (Exception $e) {
            // Log any exceptions that occur during email sending
            Log::alert($e->getMessage());
        }
    }

    // Sends scheduled email reports based on configured conditions
    protected function sendEmailReport()
    {
        try {
            $schedule = $this->app->make(Schedule::class);
           
                $schedule->call(function () {
                    if (Notifications::getDailyCount() > 0) {
                        Mail::to(config('custom-log.pm-emails'))->send(new ReportEmail());
                    }
                })->dailyAt('10:00');
            
        } catch (Exception $e) {
            // Log any exceptions that occur during email sending
            Log::alert($e->getMessage());
        }
    }

    // Publishes required configuration and migration files
    protected function publishRequiredFiles()
    {
        $this->publishes([
            __DIR__ . '/config/custom-log.php' => config_path('custom-log.php')
        ], 'config');

        $this->publishes([
            __DIR__ . '/migrations/2021_12_13_000000_create_logs_table.php' => database_path('migrations/2021_12_13_000000s_create_logs_table.php')
        ], 'migration');
    }
}
