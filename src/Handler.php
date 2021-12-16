<?php

namespace Computan\LaravelCustomLog;

use Computan\Jobs\SendEmailsJob;
use Computan\LaravelCustomLog\Notifications;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use ReflectionClass;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var string[]
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var string[]
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
            $class = new ReflectionClass($e);
            Notifications::error('exceptions', "Exception {$class} | {$e->getMessage()}", collect($e)->toArray());
            /* sending emails to define users in case of error */
            $exception = [
                "name" => get_class($e),
                "message" => $e->getMessage(),
                "file" => $e->getFile(),
                "line" => $e->getLine(),
                "type"=>"exception",
            ];
            dispatch(new SendEmailsJob($exception))->delay(5);
        });
    }
}
