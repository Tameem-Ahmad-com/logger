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

    public static function log($level, $channel = 'laravel', $content = null, $context = [])
    {
       try{

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

        DB::table(config('custom-log.mysql_table','logs'))->insert($data);
       }catch(Exception $e){
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

        return DB::table(config('custom-log.mysql_table','logs'))->whereDate('created_at', Carbon::today())->get();
    }
    /**
     * getMonthlyLogs
     *
     * @return \Illuminate\Support\Collection
     */
    public static function getMonthlyLogs()
    {

        return DB::table(config('custom-log.mysql_table','logs'))->whereMonth(
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

        return DB::table(config('custom-log.mysql_table','logs'))->where('channel', 'job')->whereMonth(
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

        return DB::table(config('custom-log.mysql_table','logs'))->where('channel', 'job')
            ->whereDate('created_at', Carbon::today())->get();
    }

    /**
     * getEmailLogs
     *
     * @return \Illuminate\Support\Collection
     */
    public static function getEmailLogs()
    {

        return DB::table(config('custom-log.mysql_table','logs'))
            ->whereDate('created_at', Carbon::today())->take(50)->get();
    }

    /**
     * getLogs
     *
     * @return \Illuminate\Support\Collection
     */
    public static function getLogs()
    {

        return DB::table(config('custom-log.mysql_table','logs'))->where('is_email_sent', 0)->get();
    }

    public static function getJobDailyCount()
    {

        return DB::table(config('custom-log.mysql_table','logs'))->where('channel', 'job')
            ->whereDate('created_at', Carbon::today())->count();
    } 
    public static function getDailyCount()
    {

        return DB::table(config('custom-log.mysql_table','logs'))
            ->whereDate('created_at', Carbon::today())->count();
    }

    public static function getJobMonthlyCount()
    {

        return DB::table(config('custom-log.mysql_table','logs'))->where('channel', 'job')->whereMonth(
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
    /**
     * getHtml
     *
     * @param  mixed $collection
     * @param  mixed $count
     * @return string
     */
    public static function getHtml(): string
    {
        $totalErrors = self::getDailyCount();
        $jobsFailed = self::getJobDailyCount();
        $appName = config('app.name');
        $url = url('exceptions');
        $message = config('custom-log.emails.message');
        return <<<HTML
                    <!DOCTYPE html>
                    <html>

                    <head>
                        <title></title>
                        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                        <meta name="viewport" content="width=device-width, initial-scale=1">
                        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
                        <style type="text/css">
                            @media screen {
                                @font-face {
                                    font-family: 'Lato';
                                    font-style: normal;
                                    font-weight: 400;
                                    src: local('Lato Regular'), local('Lato-Regular'), url(https://fonts.gstatic.com/s/lato/v11/qIIYRU-oROkIk8vfvxw6QvesZW2xOQ-xsNqO47m55DA.woff) format('woff');
                                }

                                @font-face {
                                    font-family: 'Lato';
                                    font-style: normal;
                                    font-weight: 700;
                                    src: local('Lato Bold'), local('Lato-Bold'), url(https://fonts.gstatic.com/s/lato/v11/qdgUG4U09HnJwhYI-uK18wLUuEpTyoUstqEm5AMlJo4.woff) format('woff');
                                }

                                @font-face {
                                    font-family: 'Lato';
                                    font-style: italic;
                                    font-weight: 400;
                                    src: local('Lato Italic'), local('Lato-Italic'), url(https://fonts.gstatic.com/s/lato/v11/RYyZNoeFgb0l7W3Vu1aSWOvvDin1pK8aKteLpeZ5c0A.woff) format('woff');
                                }

                                @font-face {
                                    font-family: 'Lato';
                                    font-style: italic;
                                    font-weight: 700;
                                    src: local('Lato Bold Italic'), local('Lato-BoldItalic'), url(https://fonts.gstatic.com/s/lato/v11/HkF_qI1x_noxlxhrhMQYELO3LdcAZYWl9Si6vvxL-qU.woff) format('woff');
                                }
                            }

                            /* CLIENT-SPECIFIC STYLES */
                            body,
                            table,
                            td,
                            a {
                                -webkit-text-size-adjust: 100%;
                                -ms-text-size-adjust: 100%;
                            }

                            table,
                            td {
                                mso-table-lspace: 0pt;
                                mso-table-rspace: 0pt;
                            }

                            img {
                                -ms-interpolation-mode: bicubic;
                            }

                            /* RESET STYLES */
                            img {
                                border: 0;
                                height: auto;
                                line-height: 100%;
                                outline: none;
                                text-decoration: none;
                            }

                            table {
                                border-collapse: collapse !important;
                            }

                            body {
                                height: 100% !important;
                                margin: 0 !important;
                                padding: 0 !important;
                                width: 100% !important;
                            }

                            /* iOS BLUE LINKS */
                            a[x-apple-data-detectors] {
                                color: inherit !important;
                                text-decoration: none !important;
                                font-size: inherit !important;
                                font-family: inherit !important;
                                font-weight: inherit !important;
                                line-height: inherit !important;
                            }

                            /* MOBILE STYLES */
                            @media screen and (max-width:600px) {
                                h1 {
                                    font-size: 32px !important;
                                    line-height: 32px !important;
                                }
                            }

                            /* ANDROID CENTER FIX */
                            div[style*="margin: 16px 0;"] {
                                margin: 0 !important;
                            }
                        </style>
                    </head>

                    <body style="background-color: #f4f4f4; margin: 0 !important; padding: 0 !important;">
                        <!-- HIDDEN PREHEADER TEXT -->
                        <div style="display: none; font-size: 1px; color: #fefefe; line-height: 1px; font-family: 'Lato', Helvetica, Arial, sans-serif; max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden;">
                         This email is provide you reporting about integration projects status. 
                        </div>
                        <table border="0" cellpadding="0" cellspacing="0" width="100%">
                            <!-- LOGO -->
                            <tr>
                                <td bgcolor="#9ca3af" align="center">
                                    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px;">
                                        <tr>
                                            <td align="center" valign="top" style="padding: 40px 10px 40px 10px;"> </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td bgcolor="#9ca3af" align="center" style="padding: 0px 10px 0px 10px;">
                                    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px;">
                                        <tr>
                                            <td bgcolor="#ffffff" align="center" valign="top" style="padding: 40px 20px 20px 20px; border-radius: 4px 4px 0px 0px; color: #111111; font-family: 'Lato', Helvetica, Arial, sans-serif; font-size: 48px; font-weight: 400; letter-spacing: 4px; line-height: 48px;">
                                                <h1 style="font-size: 48px; font-weight: 400; margin: 2;">{$appName}!</h1> <img src=" hhttps://mpng.subpng.com/20181119/lpf/kisspng-computer-icons-scalable-vector-graphics-portable-n-gmail-undo-send-google-mail-5bf311d368f0f6.3464410815426564674298.jpg" width="125" height="120" style="display: block; border: 0px;" />
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td bgcolor="#f4f4f4" align="center" style="padding: 0px 10px 0px 10px;">
                                    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px;">
                                        <tr>
                                            <td bgcolor="#ffffff" align="left" style="padding: 20px 30px 40px 30px; color: #666666; font-family: 'Lato', Helvetica, Arial, sans-serif; font-size: 18px; font-weight: 400; line-height: 25px;">
                                                <p style="margin: 0;">{$message}.</p>
                                            </td>
                                        </tr>
                                        <tr>
                                        <td bgcolor="#f4f4f4" align="center" style="padding: 30px 10px 0px 10px;">
                                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px;">
                                                <tr>
                                                    <td bgcolor="#9ca3af" align="center" style="padding: 30px 30px 30px 30px; border-radius: 4px 4px 4px 4px; color: white; font-family: 'Lato', Helvetica, Arial, sans-serif; font-size: 18px; font-weight: 400; line-height: 25px;">
                                                        <h1 style="margin: 0;"><a href="#" target="_blank" style="color:white;">Total Exceptions:{$totalErrors}</a></h1>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td bgcolor="#f4f4f4" align="center" style="padding: 30px 10px 0px 10px;">
                                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px;">
                                                <tr>
                                                    <td bgcolor="#9ca3af" align="center" style="padding: 30px 30px 30px 30px; border-radius: 4px 4px 4px 4px; color: white; font-family: 'Lato', Helvetica, Arial, sans-serif; font-size: 18px; font-weight: 400; line-height: 25px;">
                                                        <h1 style="margin: 0;"><a href="#" target="_blank" style="color: white;">Total Jobs failed:{$jobsFailed}</a></h1>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                     
                                        <tr>
                                        <td bgcolor="#ffffff" align="left">
                                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                <tr>
                                                    <td bgcolor="#ffffff" align="center" style="padding: 20px 30px 60px 30px;">
                                                        <table border="0" cellspacing="0" cellpadding="0">
                                                            <tr>
                                                                <td align="center" style="border-radius: 3px;" bgcolor="#9ca3af">
                                                                <a href="{$url}" target="_blank" style="font-size: 20px; font-family: Helvetica, Arial, sans-serif; color: #ffffff; text-decoration: none; color: #ffffff; text-decoration: none; padding: 15px 25px; border-radius: 2px; border: 1px solid #9ca3af; display: inline-block;">
                                                                See error details</a></td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                    </table>
                                </td>
                            </tr>
                         </table>
                    </body>

                    </html>

                HTML;
    }


    public static function getDevHtml($error)
    {
        $url = url('exceptions');
        $appName = config('app.name');
        return <<<HTML
                    
                <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">

                <table style="width: 800px;margin: 0 auto;background-color: #f9f9f9;padding: 16px;">
                <tr style="background-color:#ff4153">
                    <td><h1 style="margin-bottom:20px;text-align: center;margin: 0;font-size: 26px;padding: 20px; color: #fff;font-family: 'Roboto', sans-serif;font-weight: 700;">Exception occured</h1></td>
                </tr>
                <tr>
                    <td  style="padding-top: 20px;">
                        <h3 style="margin:0;color:#000;font-family: 'Roboto', sans-serif;font-weight: 700;padding:0 20px;">Hi, Something went wrong on {$appName}</h3>
                    </td>
                </tr>
                <tr>
                    <td>
                        <h3 style="padding:0 20px;margin:0 0 20px 0;line-height:1.8;font-size:16px;font-family: 'Roboto', sans-serif;">
                        {$error->message}
                       </h3>
                    </td>
                </tr>
                <tr>
                    <td>
                        <p  style="    background-color: #2F4F4F;color: #fff;padding: 20px;margin:0 0 20px 0;line-height:1.8;font-size:16px;font-family: 'Roboto', sans-serif;font-weight: 400;">
                        <?php dump($error->context);?>
                      </p>
                    </td>
                </tr>

                <tr>
                    <td>
                        <div style="text-align:center">
                            <a href="{$url}" style="padding:10px 30px;font-size:19px;font-family: 'Roboto', sans-serif;font-weight: 600;display: inline-block;text-decoration: none;">
                            Please see error details here 
                        </a>
                        </div>
                    </td>
                </tr>

                </table>

            
        HTML;
    }
}
