<?php

namespace Computan\LaravelCustomLog;

use Monolog\Logger;
use Illuminate\Support\Carbon;
use Monolog\Handler\GroupHandler;
use Illuminate\Support\Facades\DB;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogHandler;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\SyslogUdpHandler;
use Illuminate\Queue\Events\JobFailed;
use Monolog\Handler\RotatingFileHandler;
use Computan\LaravelCustomLog\MysqlHandler;
use Illuminate\Database\Eloquent\Collection;
use Monolog\Handler\WhatFailureGroupHandler;


class Notifications
{
    private static $channels = [];
    protected JobFailed $event;

    /**
     * Create a custom Monolog instance.
     *
     * @param  array  $config
     * @return \Monolog\Logger
     */
    public function __invoke(array $config)
    {
        return self::getSystemLogger();
    }

    public static function getChannel($channel)
    {
        if (isset(self::$channels[$channel])) {
            return self::$channels[$channel];
        } else {
            $log = new Logger($channel);

            if (config('custom-log.failsafe')) {
                $log->pushHandler(new WhatFailureGroupHandler(self::getHandlers($channel)));
            } else {
                $log->pushHandler(new GroupHandler(self::getHandlers($channel)));
            }

            self::$channels[$channel] = $log;

            return $log;
        }
    }

    public static function getHandlers($channel)
    {
        $handlers = [];

        $formatter = new LineFormatter(null, null, true, true);

        if (config('custom-log.stacktrace')) {
            $formatter->includeStacktraces(true);
        }

        if (config('custom-log.console.enable', false)) {
            $consoleHandler = new StreamHandler('php://stdout', Logger::DEBUG);
            $consoleHandler->setFormatter($formatter);
            $handlers[] = $consoleHandler;
        }

        if (config('custom-log.file.enable', true)) {
            $fileHandler = new RotatingFileHandler(storage_path() . "/logs/{$channel}.log",  0, Logger::DEBUG, true, 0666, false);
            $fileHandler->setFormatter($formatter);
            $handlers[] = $fileHandler;
        }
        if (config('custom-log.mysql.enable')) {
            $mysqlHandler = new MysqlHandler(config('custom-log.mysql.connection'), config('custom-log.mysql.table'), Logger::DEBUG, true);
            $handlers[] = $mysqlHandler;
        }

        if (config('custom-log.syslog.enable')) {
            if (config('custom-log.syslog.host')) {
                $handlers[] = new SyslogUdpHandler(config('custom-log.syslog.host'), config('custom-log.syslog.port'), LOG_USER, Logger::DEBUG, true, config('times.application_name'));
            } else {
                $handlers[] = new SyslogHandler(config('times.application_name'));
            }
        }
        return $handlers;
    }

    public static function getSystemLogger()
    {
        return self::getChannel('laravel');
    }

    public static function getSystemHandler()
    {
        if (config('custom-log.failsafe')) {
            return new WhatFailureGroupHandler(self::getSystemLogger()->getHandlers());
        } else {
            return new GroupHandler(self::getSystemLogger()->getHandlers());
        }
    }

    public static function emergency($channel = 'laravel', $content = null, $context = [])
    {
        self::log(Logger::EMERGENCY, $channel, $content, $context);
    }

    public static function alert($channel = 'laravel', $content = null, $context = [])
    {
        self::log(Logger::ALERT, $channel, $content, $context);
    }

    public static function critical($channel = 'laravel', $content = null, $context = [])
    {
        self::log(Logger::CRITICAL, $channel, $content, $context);
    }

    public static function error($channel = 'laravel', $content = null, $context = [])
    {
        self::log(Logger::ERROR, $channel, $content, $context);
    }

    public static function warning($channel = 'laravel', $content = null, $context = [])
    {
        self::log(Logger::WARNING, $channel, $content, $context);
    }

    public static function notice($channel = 'laravel', $content = null, $context = [])
    {
        self::log(Logger::NOTICE, $channel, $content, $context);
    }

    public static function info($channel = 'laravel', $content = null, $context = [])
    {
        self::log(Logger::INFO, $channel, $content, $context);
    }

    public static function debug($channel = 'laravel', $content = null, $context = [])
    {
        self::log(Logger::DEBUG, $channel, $content, $context);
    }

    public static function log($level, $channel = 'laravel', $content = null, $context = [])
    {
        $log = self::getChannel($channel);
        $log->addRecord($level, $content, $context);
    }

    public static function requestInfo(): array
    {
        $info = [];
        $info['ip'] = request()->getClientIp();
        $info['method'] = request()->server('REQUEST_METHOD');
        $info['url'] = request()->url();
        if (Auth::check()) {
            $info['userid'] = Auth::user()->id;
        }
        $input = request()->all();
        $remove = ['password', 'password_confirmation', '_token'];
        foreach ($remove as $item) {
            if (isset($input[$item])) {
                unset($input[$item]);
            }
        }
        $info['input'] = $input;
        return $info;
    }

    /**
     * getDailyLogs
     *
     * @return \Illuminate\Support\Collection
     */
    public static function getDailyLogs()
    {

        return DB::table(config('custom-log.mysql.table'))->whereDate('created_at', Carbon::today())->get();
    }
    /**
     * getMonthlyLogs
     *
     * @return \Illuminate\Support\Collection
     */
    public static function getMonthlyLogs()
    {

        return DB::table(config('custom-log.mysql.table'))->whereMonth(
            'created_at',
            Carbon::now()->format('m')
        )->get();
    }
    public static function getJobMonthlyLogs()
    {

        return DB::table(config('custom-log.mysql.table'))->where('message', 'job')->whereMonth(
            'created_at',
            Carbon::now()->format('m')
        )->get();
    }

    public static function getJobDailyLogs()
    {

        return DB::table(config('custom-log.mysql.table'))->where('message', 'job')
            ->whereDate('created_at', Carbon::today())->get();
    }

    public static function getJobDailyCount()
    {

        return DB::table(config('custom-log.mysql.table'))->where('message', 'job')
            ->whereDate('created_at', Carbon::today())->count();
    }

    public static function getJobMonthlyCount()
    {

        return DB::table(config('custom-log.mysql.table'))->where('message', 'job')->whereMonth(
            'created_at',
            Carbon::now()->format('m')
        )->count();
    }


    public function setEvent(JobFailed $event): self
    {
        $this->event = $event;

        return $this;
    }


    /**
     * toMail
     *
     * @param  mixed $exception
     * @return bool
     */
    public static function toMail($exception): bool
    {
        Mail::to(config('custom-log.emails'))->send(new \Computan\Mail\NotificationEmail($exception));
        return true;
    }

    /**
     * getHtml
     *
     * @param  mixed $collection
     * @param  mixed $count
     * @return string
     */
    public static function getHtml($collection,$count=null): string
    {
        return <<<HTML
                <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
                <table style="width: 800px;margin: 0 auto;background-color: #f9f9f9;padding: 16px;">
                <tr style="background-color:#ff4153">
                    <td><h1 style="margin-bottom:20px;text-align: center;margin: 0;font-size: 26px;padding: 20px;
                     color: #fff;font-family: 'Roboto', sans-serif;font-weight: 700;">Error occured <?php config('app.url') ?> </h1></td>
                </tr>
                <tr>
                    <td  style="padding-top: 20px;">
                        <h3 style="margin:0;color:#000;font-family: 'Roboto', sans-serif;font-weight: 700;padding:0 20px;">Hi</h3>
                    </td>
                </tr>
                <tr>
                    <td>
                        <h3 style="font-family: 'Roboto', sans-serif;font-weight: 700;padding:0 20px;">Message</h3>
                    </td>
                </tr>
                <tr>
                    <td>
                        <p style="padding:0 20px;margin:0 0 20px 0;line-height:1.8;font-size:16px;font-family: 'Roboto', sans-serif;font-weight: 400;">
                        <?php $collection->message=="job"?"Job failed please see error details":$collection->message  ?></p>
                    </td>
                </tr>
                <tr>
                    <td>
                        <p  style="    background-color: #2F4F4F;color: #fff;padding: 20px;margin:0 0 20px 0;line-height:1.8;font-size:16px;font-family: 'Roboto', sans-serif;font-weight: 400;">
                             <?php  print_r($collection->context, TRUE)?> </p>
            
                    </td>
                </tr>

                <tr>
                    <td>
                        <div style="text-align:center">
                            <a href="#" style="padding:10px 30px;font-size:19px;font-family: 'Roboto', sans-serif;font-weight: 600;display: inline-block;text-decoration: none;">Please contact with your Service Provider</a>
                        </div>
                    </td>
                </tr>

                </table>

                HTML;
    }
}
