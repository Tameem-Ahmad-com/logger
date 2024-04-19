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
        $ignoreExceptions = config('custom-log.ignore_exceptions', []);

        foreach ($ignoreExceptions as $class => $codes) {
            if ($e instanceof $class && $this->isIgnoredExceptionCode($e, $codes)) {
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
        if ($e instanceof QueryException && in_array($e->getCode(), $codes)) {
            return true;
        }

        // Add more conditions here to ignore specific exception codes based on your requirements

        return false;
    }
}
