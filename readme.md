# Laravel Custom Log & Notifications

🚀 **Laravel Custom Log & Notifications** is a robust logging and notification library designed for Laravel applications, offering seamless handling of exceptions and automatic error reporting.

## Installation

1. **Install the Package:** Use Composer to install the package:

    ```bash
    composer require notify/notification:"dev-main"
    ```

2. **Install AWS SES Package:** Use Composer to install the package:

    ```bash
    composer require aws/aws-sdk-php
    ```

3. **Publish Configuration:** Publish the configuration file:

    ```bash
    php artisan vendor:publish --provider="Notify\\LaravelCustomLog\\LaravelCustomLogServiceProvider" --tag=config
    ```

4. **Publish MySQL Migration:** Publish the MySQL migration file:

    ```bash
    php artisan vendor:publish --provider="Notify\\LaravelCustomLog\\LaravelCustomLogServiceProvider" --tag=migration
    ```

## Configuration Options

Configure the behavior of the package in the `config/custom-log.php` file. Here's a breakdown of the available options:

- 🛠️ `dev-mode`: Enable/disable development mode.
- 🛠️ `custom_log_mysql_enable`: Enable/disable logging to a MySQL database.
- 🛠️ `mysql_table`: Define the table name for logging in the MySQL database.
- 🛠️ `override_exception_handler`: Override the default Laravel exception handler.
- 📧 `emails`: Configure email settings for error reports.
- 📧 `pm-emails`: Specify project manager email addresses.
- 📧 `dev-emails`: Specify developer email addresses for notifications.
- ⚠️ `ignore_exceptions`: Specify exceptions and error codes to ignore.
- 💾 `database_connection`: Define the database connection for logging.

### SMTP/AWS SES Configuration

To use AWS SES for email notifications, configure the following environment variables in your `.env` file:

```dotenv
MAIL_MAILER=ses
MAIL_HOST=
MAIL_PORT=
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=
```

## Example to handle the exception internally

Disable the exception override in `custom-log.php` and use your own logic in `Exceptions/Handler.php`.

The below example is handling all the database exception and ignore them to log

```php
public function register()
{
    $this->reportable(function (Throwable $e) {
        if ($e instanceof QueryException) {
           return;
        } 
        Notifications::error('exceptions', $e->getMessage(), $e->getTrace());
    });
}
```

Once installed and configured, Laravel Custom Log & Notifications will handle exception reporting and notifications automatically according to your configuration settings.

👍 Happy logging and notifying with Laravel Custom Log & Notifications!
