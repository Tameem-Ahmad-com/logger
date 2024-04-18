# Laravel Custom Log & Notifications
Laravel failsafe custom logging & notification library which send notification to the developers

- Send notification to developer on each exceptions 
- Send daily report to clients
- provide a view to see all exceptions 
## Please note
This package override laravel Exception Handler and you will not able to do anything inside Handler.php to avoid this use below steps.

- open `config/custom-log.php` & `override_exception_handler=false` if you want to handle the exceptions by yourself and put this code into the register method.

```php
<?php
$this->reportable(function (Throwable $e) {
          // your own code 

            \Notify\LaravelCustomLog\Notifications\Notifications::error('exceptions', "{$e->getMessage()}", $e->getTrace());
         });

```

- by default this package will override the exception handler
## Required package in case of AWS SES
`composer require aws/aws-sdk-php`
## Update config.php 


```php
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
    'custom_log_mysql_enable' => true,

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
        'cc' => ['youremail@computan.net']
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
    'pm-emails' => ['your-pm-email@computan.net'],

    /*
    |--------------------------------------------------------------------------
    | Developer Emails
    |--------------------------------------------------------------------------
    |
    | Specify the email addresses of developers who will receive notifications
    | on each exception.
    |
    */
    'dev-emails' => ['youremail@computan.net'],

    /*
    |--------------------------------------------------------------------------
    | Ignore Exception Codes
    |--------------------------------------------------------------------------
    |
    | Specify exception codes that should be ignored and not reported.
    |
    */
    'ignore_exception_codes' => [123,40001],

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


```


[Please see docs](https://getcomposer.org/doc/04-schema.md#repositories)
## Installation using packagist

`composer require notify/notification:"dev-main"`


Publish Config

`php artisan vendor:publish --provider="Notify\\LaravelCustomLog\\LaravelCustomLogServiceProvider" --tag=config`

Publish MySQL Migration

`php artisan vendor:publish --provider="Notify\\LaravelCustomLog\\LaravelCustomLogServiceProvider" --tag=migration`


## Basic Usage

Add this package into your project & edit config file which comes with this package

## Replace Laravel log (Laravel <= 5.5)

Edit your `bootstrap/app.php`, add this before returning the application

# In simple you just need paste the above code in config.php and update emails nothing else is required