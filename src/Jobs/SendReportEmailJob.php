<?php

namespace Notify\LaravelCustomLog\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Notify\LaravelCustomLog\Notifications;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class SendReportEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    /**
     * e
     *
     * @var mixed
     */
    protected $e;
    public function __construct()
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        if (Notifications::getDailyCount() > 0 || Notifications::getJobDailyCount() > 0) {
            Mail::send([], [], function ($message) {
                $message
                    ->to(config('custom-log.emails'))
                    ->from(config('mail.from.address'))
                    ->subject('Error Notification')
                    ->setBody(Notifications::getHtml(), 'text/html');
            });
        }
    }
}
