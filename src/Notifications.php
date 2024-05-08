<?php

namespace Notify\LaravelCustomLog;

use Exception;
use Monolog\Logger;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\Events\JobFailed;




class Notifications
{

    protected JobFailed $event;


    public static function emergency($channel = 'laravel', $content = null, $context = [])
    {
        self::log('emergency', $channel, $content, $context);
    }

    public static function alert($channel = 'laravel', $content = null, $context = [])
    {
        self::log('alert', $channel, $content, $context);
    }

    public static function critical($channel = 'laravel', $content = null, $context = [])
    {
        self::log('critical', $channel, $content, $context);
    }

    public static function error($channel = 'laravel', $content = null, $context = [])
    {
        self::log('error', $channel, $content, $context);
    }

    public static function warning($channel = 'laravel', $content = null, $context = [])
    {
        self::log('warning', $channel, $content, $context);
    }

    public static function notice($channel = 'laravel', $content = null, $context = [])
    {
        self::log('notice', $channel, $content, $context);
    }

    public static function info($channel = 'laravel', $content = null, $context = [])
    {
        self::log('info', $channel, $content, $context);
    }

    public static function debug($channel = 'laravel', $content = null, $context = [])
    {
        self::log('debug', $channel, $content, $context);
    }

    public static function log($level, $channel, $content = null, $context = [])
    {
        try {

            $loggingType = config('custom-log.logging_type', 'log');

            // Check if the logging type is 'exception' and the level is ERROR or higher
            if ($loggingType === 'exception' && Logger::toMonologLevel($level) >= Logger::ERROR) {
                // Save the exception to the Monolog and then to the database
                $logger = new Logger($channel);
                $logger->pushHandler(new MysqlHandler());
                $logger->log($level, $content, $context);
            }

            // Save the log to the database regardless of the logging type
            $data = [
                'instance' => gethostname(),
                'message' => $content,
                'channel' => $channel,
                'level' => $level,
                'level_name' => strtoupper($level),
                'context' => json_encode($context),
                'remote_addr' => $_SERVER['REMOTE_ADDR'] ?? null,
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
                'created_by' => Auth::id() > 0 ? Auth::id() : null,
                'created_at' => now(),
            ];

            DB::table(config('custom-log.mysql_table', 'logs'))->insert($data);
        } catch (Exception $e) {
            Log::error('Error occurred while logging: ' . $e->getMessage());
        }
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

        return DB::table(config('custom-log.mysql_table', 'logs'))->whereBetween('created_at', [Carbon::now()->subHours(10), Carbon::now()])
            ->get();
    }
    /**
     * getMonthlyLogs
     *
     * @return \Illuminate\Support\Collection
     */
    public static function getMonthlyLogs()
    {

        return DB::table(config('custom-log.mysql_table', 'logs'))->whereMonth(
            'created_at',
            Carbon::now()->format('m')
        )->get();
    }
    /**
     * getJobMonthlyLogs
     *
     * @return \Illuminate\Support\Collection
     */
    public static function getJobMonthlyLogs()
    {

        return DB::table(config('custom-log.mysql_table', 'logs'))->where('channel', 'job')->whereMonth(
            'created_at',
            Carbon::now()->format('m')
        )->get();
    }

    /**
     * getJobDailyLogs
     *
     * @return \Illuminate\Support\Collection
     */
    public static function getJobDailyLogs()
    {

        return DB::table(config('custom-log.mysql_table', 'logs'))->where('channel', 'job')
            ->whereBetween('created_at', [Carbon::now()->subHours(10), Carbon::now()])->get();
    }

    /**
     * getEmailLogs
     *
     * @return \\Illuminate\Support\Collection
     */
    public static function getEmailLogs()
    {

        DB::statement("SET SQL_MODE=''");
        $query = DB::table(config('custom-log.mysql_table', 'logs'));
        if (config('custom-log.logging_type') == 'log') {
            $query->whereIn('channel', config('custom-log.channel'));
        }
        $query->whereBetween('created_at', [Carbon::now()->subHours(10), Carbon::now()])
            ->groupBy('message')
            ->take(50);


        return $query->get(['*']);

    }

    /**
     * getLogs
     *
     * @return \Illuminate\Support\Collection
     */
    public static function getLogs()
    {

        return DB::table(config('custom-log.mysql_table', 'logs'))
            ->whereBetween('created_at', [Carbon::now()->subHours(10), Carbon::now()])->where('is_email_sent', 0)->get();
    }

    public static function getJobDailyCount()
    {

        return DB::table(config('custom-log.mysql_table', 'logs'))->where('channel', 'job')
            ->whereBetween('created_at', [Carbon::now()->subHours(10), Carbon::now()])->count();
    }
    public static function getDailyCount()
    {

        return DB::table(config('custom-log.mysql_table', 'logs'))
            ->whereBetween('created_at', [Carbon::now()->subHours(10), Carbon::now()])->count();
    }

    public static function getJobMonthlyCount()
    {

        return DB::table(config('custom-log.mysql_table', 'logs'))->where('channel', 'job')->whereMonth(
            'created_at',
            Carbon::now()->format('m')
        )->count();
    }


    public function setEvent(JobFailed $event): self
    {
        $this->event = $event;

        return $this;
    }


    public static function toMail($data): bool
    {
        Mail::send(
            ['html' => __DIR__ . '/emails/exception.html'],
            ['collection' => $data],
            function ($message) {
                $message->to(config('custom-log.emails'))->from(config('mail.from.address'))
                    ->subject('Daily error reporting');
            }
        );
        return true;
    }
}
