<?php

namespace Notify\LaravelCustomLog;

use Exception;
use Monolog\Logger;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Monolog\Formatter\LineFormatter;
use Illuminate\Support\Facades\Config;
use Monolog\Handler\AbstractProcessingHandler;

class MysqlHandler extends AbstractProcessingHandler
{
    protected $table;
    protected $connection;

    public function __construct($level = Logger::DEBUG, $bubble = true)
    {
        parent::__construct($level, $bubble);

        $this->connection = Config::get('custom-log.database_connection', 'default');
        $this->table = Config::get('custom-log.mysql_table', 'logs');
    }

    protected function write(\Monolog\LogRecord $record): void
    {
        try {
            $data = [
                'instance'    => gethostname(),
                'message' => $record->message,
                'context' => $record->context,
                'level' => $record->level->value,
                'level_name' => $record->level->getName(),
                'channel' => $record->channel,
                'remote_addr' => isset($_SERVER['REMOTE_ADDR'])     ? $_SERVER['REMOTE_ADDR'] : null,
                'user_agent'  => isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT']  : null,
                'created_by'  => Auth::id() > 0 ? Auth::id() : null,
                'created_at'  => $record->datetime->format('Y-m-d H:i:s')
            ];

            DB::connection($this->connection)->table($this->table)->insert($data);
        } catch (Exception $e) {
            Log::alert($e->getMessage());
        }
    }
}
