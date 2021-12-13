<?php 
namespace Computan\Logging;

use Monolog\Logger;

class Myslogger{
/**
     * Create a custom Monolog instance.
     *
     *
     * @param  array  $config
     * @return \Monolog\Logger
     */
    public function __invoke(array $config){
        $logger = new Logger("MysqlLoggerHandler");
        return $logger->pushHandler(new MysqlLoggerHandler());
    }
}