<?php

namespace Notify\LaravelCustomLog\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Notify\LaravelCustomLog\Notifications;

class ReportEmail extends Mailable
{
    use Queueable, SerializesModels;

  
    public $totalErrors;
    public $jobsFailed;
    public $message;
    public $exceptions;

    public function __construct()
    {
        $this->totalErrors = Notifications::getDailyCount();
        $this->jobsFailed = Notifications::getJobDailyCount();
        $this->message = config('custom-log.emails.message');
        $this->exceptions=Notifications::getEmailLogs();
    }

    public function build()
    {
        return $this->view('CustomLog::emails.report')->subject(config('custom-log.emails.subject'));

    }
}