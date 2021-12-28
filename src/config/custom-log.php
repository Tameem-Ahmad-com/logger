<?php

return [
    'dev-mode'=>true,
    'failsafe' => env('CUSTOM_LOG_FAILSAFE', true),
    'custom_log_mysql_enable'=>env('CUSTOM_LOG_MYSQL_ENABLE',true),
    'stacktrace' => env('CUSTOM_LOG_STACKTRACE', false),
    /*
    * command could be daily() , weekly() , monthly() ,hourly()
    * @see https://laravel.com/docs/8.x/scheduling#scheduling-artisan-commands
     */
    'command'=>'daily()',
    'emails'=>[
     'subject'=>' MSWA gestalt Integration: Error Report',
     'message'=>'Hi,I trust you are well.  Here is the report of exceptions for {date("Y-m-d")}.',
    ],
    'console' => [
        'enable' => env('CUSTOM_LOG_CONSOLE_ENABLE', false),
    ],
    'file' => [
        'enable' => env('CUSTOM_LOG_FILE_ENABLE', true),
    ],
    'mysql' => [
        'enable' => env('CUSTOM_LOG_MYSQL_ENABLE', false),
        'connection' => env('DB_LOG_CONNECTION', 'mysql'),
        'table' => env('DB_LOG_TABLE', 'logs'),
    ],
   
    'syslog' => [
        'enable' => env('CUSTOM_LOG_SYSLOG_ENABLE', false),
        'host' => env('CUSTOM_LOG_SYSLOG_HOST'),
        'port' => env('CUSTOM_LOG_SYSLOG_PORT', 514),
    ],
    /* enlist all comma seprated email for PM and other to send daily report */
    'pm-emails'=>['test@gmail.com'],
    /* enlist all developers where and they get notification on each exception */
    'dev-emails'=>['tshahzad@computan.net'],
    
];
