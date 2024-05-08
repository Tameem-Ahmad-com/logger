<?php

namespace Notify\LaravelCustomLog;

use Throwable;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Notify\LaravelCustomLog\Notifications;
use PDOException;

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

            Notifications::error('critical', $e->getMessage(), $e->getTrace());
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
