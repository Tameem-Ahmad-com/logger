<?php

namespace Computan\LaravelCustomLog\Jobs;

use Computan\LaravelCustomLog\Notifications;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class SendDailyFailedJobsEmailJob implements ShouldQueue
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
       
       
        if (Notifications::getJobDailyCount()>0) {
            foreach (Notifications::getJobDailyLogs() as $error) {
               Mail::send([], [], function ($message) use($error) {
                    $message
                        ->to(config('custom-log.emails'))
                        ->from(config('mail.from.address'))
                        ->subject('Error Notification')
                        ->setBody(Notifications::getHtml($error), 'text/html');
                });
                $record = DB::table(config('custom-log.mysql.table'))->find($error->id);
                if (!is_null($record)) {
                    DB::table(config('custom-log.mysql.table'))->where('id',$error->id)->update([
                        'is_email_sent' => 1
                    ]);
                }
            }
        }
    }
}
