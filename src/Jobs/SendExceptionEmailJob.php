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

class SendExceptionEmailJob implements ShouldQueue
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

        $errors = DB::table(config('custom-log.mysql.table'))->where('is_email_sent', 0)->get();
        if (!empty($errors)) {
            foreach ($errors as $error) {
                Mail::send([], [], function ($message) use($error) {
                    $message
                        ->to(config('dev-emails'))
                        ->from(config('mail.from.address'))
                        ->subject(config('custom-log.emails.subject'))
                        ->setBody(Notifications::getDevHtml($error), 'text/html');
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