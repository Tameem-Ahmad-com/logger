<?php

namespace Notify\LaravelCustomLog\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Notify\LaravelCustomLog\Notifications;

class ExceptionEmail extends Mailable
{
    use Queueable, SerializesModels;

    protected $exception;
   

    public function __construct($exception)
    {
       $this->exception=$exception;
      
    }

    public function build()
    {
        $email= $this->view('CustomLog::emails.exception')->subject(config('custom-log.emails.subject'))
        ->from(config('mail.from.address'))->with(['exception'=>$this->exception]);
        if (!empty(config('custom-log.emails.cc'))) {
            $email->cc(config('custom-log.emails.cc'));
        }
        return $email;
        

    }
}