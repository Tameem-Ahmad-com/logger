<?php

return [
    'dev-mode' => true,
    'failsafe' => env('CUSTOM_LOG_FAILSAFE', true),
    'custom_log_mysql_enable' => env('CUSTOM_LOG_MYSQL_ENABLE', true),
    'stacktrace' => env('CUSTOM_LOG_STACKTRACE', false),
    /*
    Default will be daily but you can pass parameters here according to your need
     * * * * *  command to execute
        ┬ ┬ ┬ ┬ ┬
        │ │ │ │ │
        │ │ │ │ │
        │ │ │ │ └───── day of week (0 - 7) (0 to 6 are Sunday to Saturday, or use names; 7 is Sunday, the same as 0)
        │ │ │ └────────── month (1 - 12)
        │ │ └─────────────── day of month (1 - 31)
        │ └──────────────────── hour (0 - 23)
        └───────────────────────── min (0 - 59)
     */
    'command' => '',
    /* email related seeting */
    'emails' => [
        'subject' => ' MSWA gestalt Integration: Error Report',
        'message' => 'Hi,I trust you are well.  Here is the report of exceptions for '.date("Y-m-d").'.',
    ],
    /* enlist all comma seprated email for PM and other to send daily report */
    'pm-emails' => ['tshahzad@computan.net'],
    /* enlist all developers where and they get notification on each exception */
    'dev-emails' => ['tshahzad@computan.net'],

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

];
