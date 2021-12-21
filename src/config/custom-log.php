<?php

return [
    'prefix' => 'notify',
    'middleware' => ['web'],
    'failsafe' => env('CUSTOM_LOG_FAILSAFE', true),
    'custom_log_mysql_enable'=>env('CUSTOM_LOG_MYSQL_ENABLE',true),
    'stacktrace' => env('CUSTOM_LOG_STACKTRACE', false),
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
    /* comma seprated list of emails */
    'emails'=>['test@gmail.com'],
    
];
