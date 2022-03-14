<?php

namespace Notify\LaravelCustomLog\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Notify\LaravelCustomLog\Notifications;

class ReportEmail extends Mailable
{
    use Queueable, SerializesModels;


    protected $totalErrors;
    protected $jobsFailed;
    protected $exceptions;

    public function __construct()
    {
        $this->totalErrors = Notifications::getDailyCount();
        $this->jobsFailed = Notifications::getJobDailyCount();
        $this->exceptions = Notifications::getEmailLogs();
    }

    public function build()
    {
        $that = $this->view('CustomLog::emails.report')->subject(config('custom-log.emails.subject'))
        ->from(config('mail.from.address'))->with([
            'exceptions' => $this->exceptions,
            'totalErrors' => $this->totalErrors,
            'jobsFailed' => $this->jobsFailed,

        ]);
        if (!empty(config('custom-log.emails.cc'))) {
            $that->cc(config('custom-log.emails.cc'));
        }
        return $that;
    }
}
