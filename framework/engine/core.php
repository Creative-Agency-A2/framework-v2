<?php

namespace framework\engine;

class core
{
    private static $_kernelIsInited = false;

    public function __construct()
    {
        if (!self::$_kernelIsInited) $this->_kernelInit();
    }

    private function _kernelInit()
    {
        $this->includeApplicationConfig('configs\general.php');
        
        require_once __PATH__ . "/framework/configs/general.php";
        require_once __PATH__ . "/framework/configs/storage.php";

        $di = new \framework\engine\container\container();

        $di->set('exception_logger', function ($obj, $params) {
            $handler = new \framework\libraries\logger\handler\streamHandler(__DIR_LOGS__ . 'debug.log', \framework\libraries\logger\logger::DEBUG);
            $logger = new \framework\libraries\logger\logger('exception_logger');
            $logger->pushHandler($handler);
            return $logger;
        });

        $di->set('localStorage', function($obj, $params){
            $localStorage = new \framework\libraries\storage\drivers\local();
            return new \framework\libraries\storage\storage( $localStorage );
        });

        $di->set('response', function ($obj, $params) {
            return new \framework\libraries\http\response();
        });

        $di->set('loader', function ($obj, $params) {
            return new loader($obj);
        });

        $exceptionHandler = new \framework\libraries\exception\handler\exceptionHandler($di->get('exception_logger'));
        $exception = new \framework\libraries\exception\ExceptionHandler($exceptionHandler);
        $exception->init();

        new providers($di);

        //$http = $di->get('http');
        //$response = $http->request('GET', 'https://moe-pravo.bitrix24.ru/rest/10/zx5vg41xa7n2ho08/crm.address.fields')->getBody();

    }

    function includeApplicationConfig($filename, $context = null) {
        if(file_exists(__DIR_APP__ . $filename))
            return require_once __DIR_APP__ . $filename;
    }
}
