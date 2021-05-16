<?php 
    namespace framework\providers;

    class logsProvider extends \framework\engine\provider\provider
    {
        public function register($di)
        {
            $di->set('database_logger', function(&$obj, $params){
                $handler = new \framework\libraries\logger\handler\streamHandler(__DIR_LOGS__ . 'database.log', \framework\libraries\logger\logger::DEBUG);
                $logger = new \framework\libraries\logger\logger('database_logger');
                $logger->pushHandler($handler);
                return $logger;
            });
        }
        public function boot($di)
        {
        }
    }