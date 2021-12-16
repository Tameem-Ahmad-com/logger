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
    public static function toMail($exception):bool
    {
        Mail::to(config('custom-log.emails'))->send(new \Computan\Mail\NotificationEmail($exception));
        return true;
    }
}
