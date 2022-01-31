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
use Notify\LaravelCustomLog\Mail\ExceptionEmail;

class SendExceptionEmailJob implements ShouldQueue, ShouldBeUnique
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
    /**
     * The unique ID of the job.
     *
     * @return string
     */
    public function uniqueId()
    {
        return substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, 10);
    }
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
                Mail::to(config('custom-log.dev-emails'))->send(new ExceptionEmail($error));
                $record = DB::table(config('custom-log.mysql.table'))->find($error->id);
                if (!is_null($record)) {
                    DB::table(config('custom-log.mysql.table'))->where('id', $error->id)->update([
                        'is_email_sent' => 1
                    ]);
                }
            }
        }
    }
}
