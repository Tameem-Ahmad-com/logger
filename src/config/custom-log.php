<?php
return [
    /*
    |--------------------------------------------------------------------------
    | Development Mode
    |--------------------------------------------------------------------------
    |
    | Set this option to true if you want to enable development mode.
    | In development mode, additional debug information may be displayed.
    |
    */
    'dev-mode' =>false,

    /*
    |--------------------------------------------------------------------------
    | Enable MySQL Logging
    |--------------------------------------------------------------------------
    |
    | Set this option to true if you want to enable logging to a MySQL database.
    |
    */
    'custom_log_mysql_enable' => false,

    /*
    |--------------------------------------------------------------------------
    | MySQL Table
    |--------------------------------------------------------------------------
    |
    | This option defines the table name to use for logging in the MySQL
    | database. You can customize this value based on your database schema.
    |
    */
    'mysql_table' => 'logs',

    /*
    |--------------------------------------------------------------------------
    | Override Exception Handler
    |--------------------------------------------------------------------------
    |
    | Set this option to true if you want to override the default Laravel
    | exception handler with a custom one provided by this package.
    |
    */
    'override_exception_handler' => true,

    /*
    |--------------------------------------------------------------------------
    | Email Settings
    |--------------------------------------------------------------------------
    |
    | Configure the subject, message, and CC recipients for error reports sent
    | via email.
    |
    */
    'emails' => [
        'subject' => 'Error Report',
        'message' => 'Hi',
        'cc' => ['tshahzad@computan.net']
    ],

    /*
    |--------------------------------------------------------------------------
    | Project Manager Emails
    |--------------------------------------------------------------------------
    |
    | Specify the email addresses of project managers or recipients who will
    | receive daily error reports.
    |
    */
    'pm-emails' => ['support@hellokongo.com'],

    /*
    |--------------------------------------------------------------------------
    | Developer Emails
    |--------------------------------------------------------------------------
    |
    | Specify the email addresses of developers who will receive notifications
    | on each exception.
    |
    */
    'dev-emails' => ['tshahzad@computan.net'],

    /*
    |--------------------------------------------------------------------------
    | Ignore Exception Codes
    |--------------------------------------------------------------------------
    |
    | Specify exception codes that should be ignored and not reported.
    |
    */
    'ignore_exception_codes' => [123],

    /*
    |--------------------------------------------------------------------------
    | Database Connection
    |--------------------------------------------------------------------------
    |
    | This option defines the database connection to use for logging. You can
    | specify the name of the database connection as defined in your
    | database.php configuration file.
    |
    */
    'database_connection' => env('CUSTOM_LOG_DB_CONNECTION', 'default'),
];
