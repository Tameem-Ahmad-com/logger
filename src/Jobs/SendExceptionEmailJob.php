<?php

namespace Computan\LaravelCustomLog\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
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
                $html = '<h1>Error Occured on ' . config('app.url') . '</h1><br><br>
                <h4>Here is error details</h4><br>
        
                <p style="background: rgba(0,0,0,0.5);color:white">' . print_r($error->context, TRUE) . '</p><br>
                  please contact with development team.';
                  
                Mail::send([], [], function ($message) use ($html) {
                    $message
                        ->to(config('custom-log.emails'))
                        ->from(config('mail.from.address'))
                        ->subject('Error Notification')
                        ->setBody($html, 'text/html');
                });
                $record = DB::table(config('custom-log.mysql.table'))->find($error->id);
                if (!is_null($record)) {
                    $record->update([
                        'is_email_sent' => 1
                    ]);
                }
            }
        }
    }
}
