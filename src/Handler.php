<?php

namespace Notify\LaravelCustomLog;

use Throwable;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Notify\LaravelCustomLog\Notifications;

class Handler extends ExceptionHandler
{
    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            if ($this->shouldIgnoreException($e)) {
                return;
            }

            Notifications::error('exceptions', $e->getMessage(), $e->getTrace());
        });
    }

    /**
     * Determine if the exception should be ignored.
     *
     * @param  \Throwable  $e
     * @return bool
     */
    private function shouldIgnoreException(Throwable $e): bool
    {
        if ($e instanceof QueryException && $this->isDeadlockException($e)) {
            return true;
        }

        // Add more conditions here to ignore specific exceptions based on your requirements

        return false;
    }

    /**
     * Determine if the exception is a deadlock exception.
     *
     * @param  \Illuminate\Database\QueryException  $e
     * @return bool
     */
    private function isDeadlockException(QueryException $e): bool
    {
        return in_array($e->getCode(), config('custom-log.ignore_exception_codes', []));
    }
}
