# Laravel CustomLog
Laravel failsafe custom logging & notification library which send notification to the developers

- Log to multiple destinations
- Log to Console (STDOUT)
- Log to File
- Log to MySQL
- Log to Redis
- Log to syslog (Local/Remote)
- Log to Graylog (TCP/UDP)
- (Optional) Failsafe (Don't throw any exceptions in case logger fails)
- (Optional) Replace Laravel log (Laravel <= 5.5)
- (Optional) Register as Laravel logger channel (Laravel >= 5.6)

## Installation

`composer require computan/debbuer`

On Laravel 5.4 and below, add the ServiceProvider to your `config/app.php`

`Computan\LaravelCustomLog\LaravelCustomLogServiceProvider::class`

Publish Config

`php artisan vendor:publish --provider="Computan\\LaravelCustomLog\\LaravelCustomLogServiceProvider" --tag=config`

Publish MySQL Migration

`php artisan vendor:publish --provider="Computan\\LaravelCustomLog\\LaravelCustomLogServiceProvider" --tag=migration`
## Emails
Edit config  custom-log.php email key , add comma seprated emails and app will notify to them 
```
 'emails'=>['test@gmail.com','test1@gmail.com'],
```
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
    $monolog->pushHandler(Computan\LaravelCustomLog\LaravelCustomLogServiceProvider::getSystemHandler());
});
```
## Register as Laravel logger channel (Laravel >= 5.6)

Edit your `config/logging.php`, add this to the `channels` array

```
'customlog' => [
    'driver' => 'custom',
    'via' => Computan\LaravelCustomLog\Notifications::class,
]
```
