<?php

return [

    /*
     * If set to false, no logs will be saved to the database.
     */
    'enabled' => env('DEBBUGER_ENABLED', true),
  /*
     * for now its only support mysql connection.
     */
    'channels' => [
        'database' => [
            'table' => 'debugging_logs',
            'connection' => 'mysql',
         ],


    ],


];
