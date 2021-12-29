# Laravel Custom Log & Notifications
Laravel failsafe custom logging & notification library which send notification to the developers

- Send notification to developer on each exceptions 
- Send daily report to clients
- provide a view to see all exceptions 



## Installation using private repo

`{
    "require": {
        "vendor/my-private-repo": "dev-master"
    },
    "repositories": [
        {
            "type": "vcs",
            "url":  "git@bitbucket.org:vendor/my-private-repo.git"
        }
    ]
}`
The only requirement is the installation of SSH keys for a git client.

[Please see docs](https://getcomposer.org/doc/04-schema.md#repositories)
## Installation using packagist

`composer require notify/notification`

On Laravel 5.4 and below, add the ServiceProvider to your `config/app.php`

`Notify\LaravelCustomLog\LaravelCustomLogServiceProvider::class`

Publish Config

`php artisan vendor:publish --provider="Notify\\LaravelCustomLog\\LaravelCustomLogServiceProvider" --tag=config`

Publish MySQL Migration

`php artisan vendor:publish --provider="Notify\\LaravelCustomLog\\LaravelCustomLogServiceProvider" --tag=migration`
## Emails
Edit config  custom-log.php email key , add comma seprated emails and app will notify to them 
```
 'pm-emails'=>['test@gmail.com','test1@gmail.com'],
```
## Developer Mode
```
 'dev-mode' => true,
 'dev-emails'=>['test@gmail.com','test1@gmail.com'],
```
## Customizeable emails & commands 
```
  'command' => '*****',
    /* email related seeting */
    'emails' => [
        'subject' => ' MSWA gestalt Integration: Error Report',
        'message' => 'Hi,I trust you are well.  Here is the report of exceptions for '.date("Y-m-d").'.',
    ],

```
 * * * * *  command to execute
        ┬ ┬ ┬ ┬ ┬
        │ │ │ │ │
        │ │ │ │ │
        │ │ │ │ └───── day of week (0 - 7) (0 to 6 are Sunday to Saturday, or use names; 7 is Sunday, the same as 0)
        │ │ │ └────────── month (1 - 12)
        │ │ └─────────────── day of month (1 - 31)
        │ └──────────────────── hour (0 - 23)
        └───────────────────────── min (0 - 59)

## Choose Log Destinations

Add config into `.env`, you may enable multiple destinations

### File

- CUSTOM_LOG_FILE_ENABLE (true|false, default=true)

### MySQL

- CUSTOM_LOG_MYSQL_ENABLE (true|false, default=false)
- DB_LOG_CONNECTION (connection defined in database.php, default=mysql)
- DB_LOG_TABLE (default=logs)


## Basic Usage

Add this package into your project & edit config file which comes with this package

## Replace Laravel log (Laravel <= 5.5)

Edit your `bootstrap/app.php`, add this before returning the application

```
$app->configureMonologUsing(function ($monolog) {
    $monolog->pushHandler(Notify\LaravelCustomLog\LaravelCustomLogServiceProvider::getSystemHandler());
});
```
## Register as Laravel logger channel (Laravel >= 5.6)

Edit your `config/logging.php`, add this to the `channels` array

```
'customlog' => [
    'driver' => 'custom',
    'via' => Notify\LaravelCustomLog\Notifications::class,
]
```
