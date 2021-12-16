<?php

namespace Computan\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;


class NotificationEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $exception;

    public function __construct($exception)
    {
       $this->exception=$exception;
    }

    public function build()
    {
        return $this->view('notifications::email.exception')->subject('Error notificstion from '.config('app.name'));

    }
}