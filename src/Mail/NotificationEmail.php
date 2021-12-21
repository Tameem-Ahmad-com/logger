<?php

namespace Notify\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Notify\LaravelCustomLog\Notifications;

class NotificationEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $exception;
    public $totalErrors;
    public $jobsFailed;

    public function __construct($exception)
    {
       $this->exception=$exception;
       $this->totalErrors=Notifications::getDailyCount();
       $this->jobsFailed=Notifications::getJobDailyCount();
    }

    public function build()
    {
        return $this->view('notifications::email.exception')->subject('Error report from '.config('app.name'));

    }
}