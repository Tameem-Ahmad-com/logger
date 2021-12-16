<?php

namespace Computan\Console\Commands;

use Computan\Jobs\SendExceptionEmailJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SendErrorEmailCommand extends Command
{

    protected $signature = 'send:error-email';

    protected $description = 'forward email to developers and client about error';

    public function handle()
    {
        $errors=DB::table(config('custom-log.mysql.table'))->where('is_email_sent',0)->get();
        if(!empty($errors)){
            foreach($errors as $error){
                dispatch(new SendExceptionEmailJob($error));
            }
           
        }
    }
}
