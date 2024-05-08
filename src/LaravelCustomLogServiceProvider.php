<?php

namespace Notify\LaravelCustomLog;

use Exception;
use Throwable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Support\ServiceProvider;
use Illuminate\Console\Scheduling\Schedule;
use Notify\LaravelCustomLog\Mail\ReportEmail;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Notify\LaravelCustomLog\Mail\ExceptionEmail;

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
                    try {
                        if ($this->shouldIgnoreException($event->exception)) {
                            return;
                        }
                        Notifications::error('job', $event->exception->getMessage(), $event->exception->getTrace());
                    } catch (Exception $e) {
                        Log::error('Error on Queue failing', [
                            $e->getMessage()
                        ]);
                    }
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
                        $this->clearLogs();
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
                        $errors = DB::table(config('custom-log.mysql_table', 'logs'))->where('is_email_sent', 0)->get();

                        if (!empty($errors)) {
                            foreach ($errors as $error) {
                                Mail::to(config('custom-log.dev-emails'))->send(new ExceptionEmail($error));
                                $record = DB::table(config('custom-log.mysql_table', 'logs'))->find($error->id);
                                if (!is_null($record)) {
                                    DB::table(config('custom-log.mysql_table', 'logs'))->where('id', $error->id)->update([
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

    protected function clearLogs()
    {
        try {
            // Fetch configuration values
            $deleteRecordsOlderThanDays = config('custom-log.delete_records_older_than_days', null);
            $truncateAfter = config('custom-log.truncate_after', null);

            $schedule = $this->app->make(Schedule::class);

            $schedule->call(function () use ($deleteRecordsOlderThanDays, $truncateAfter) {
                // Disable foreign key checks temporarily
                DB::statement('SET FOREIGN_KEY_CHECKS=0;');

                // If delete_records_older_than_days is set and greater than 0, delete records older than the specified days
                if ($deleteRecordsOlderThanDays && $deleteRecordsOlderThanDays > 0) {
                    $thresholdDate = now()->subDays($deleteRecordsOlderThanDays);
                    DB::table(config('custom-log.mysql_table'))->where('created_at', '<', $thresholdDate)->delete();
                }

                // If truncate_after is set and greater than 0, truncate the table if the record count exceeds the threshold
                if ($truncateAfter && $truncateAfter > 0) {
                    $recordCount = DB::table(config('custom-log.mysql_table'))->count();
                    if ($recordCount > $truncateAfter) {
                        DB::table(config('custom-log.mysql_table'))->truncate();
                    }
                }

                // Re-enable foreign key checks
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            })->daily();
        } catch (Exception $e) {
            // Log any errors
            Log::error('Error occurred while clearing logs: ' . $e->getMessage());
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

    /**
     * Determine if the exception should be ignored.
     *
     * @param  \Throwable  $e
     * @return bool
     */
    private function shouldIgnoreException(Throwable $e): bool
    {
        $ignoreExceptions = config('custom-log.ignore_exceptions', []);
        $ignoreContains = config('custom-log.ignore_contains', []);

        // Check if the exception message contains any ignored substrings
        foreach ($ignoreContains as $class => $substrings) {
            if ($e instanceof $class && $this->containsIgnoredSubstring($e, $substrings)) {
                return true;
            }
        }

        // Check if the exception code matches any ignored codes
        foreach ($ignoreExceptions as $class => $codes) {
            if ($e instanceof $class && $this->isIgnoredExceptionCode($e, $codes)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine if the exception message contains ignored substrings.
     *
     * @param  \Throwable  $e
     * @param  array  $substrings
     * @return bool
     */
    private function containsIgnoredSubstring(Throwable $e, array $substrings): bool
    {
        $message = $e->getMessage();

        foreach ($substrings as $substring) {
            if (stripos($message, $substring) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine if the exception code should be ignored.
     *
     * @param  \Throwable  $e
     * @param  array  $codes
     * @return bool
     */
    private function isIgnoredExceptionCode(Throwable $e, array $codes): bool
    {
        return (in_array('*', $codes) || in_array($e->getCode(), $codes));
    }
}
