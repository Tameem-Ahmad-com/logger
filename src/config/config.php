<?php

return [

    /*
     * If set to false, no logs will be saved to the database.
     */
    'enabled' => env('SEND_NOTIFICATION', true),
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
