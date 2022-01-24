<?php

namespace Notify\LaravelCustomLog\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Notify\LaravelCustomLog\Notifications;

class ExceptionEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $exception;
   

    public function __construct($exception)
    {
       $this->exception=$exception;
      
    }

    public function build()
    {
        return $this->view('CustomLog::emails.exception')->subject(config('custom-log.emails.subject'));

    }
}