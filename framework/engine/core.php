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

        $exceptionHandler = new \framework\libraries\exception\handler\exceptionHandler($di->get('exception_logger'));
        $exception = new \framework\libraries\exception\ExceptionHandler($exceptionHandler);
        $exception->init();

        $this->loadProviders($di);
    }

    function loadProviders($di)
    {
        $dir = scandir(__PATH__ . '/framework/providers');
        $dir = array_splice($dir, 2);

        for ($i = 0, $quantity = count($dir); $i < $quantity; $i++) {
            $name = '\\framework\providers\\' . explode('.', $dir[$i])[0];
            $entity = new $name();
            $entity->register($di);
            $entity->boot($di);
        }

        $dir = scandir(__DIR_PROVIDERS__);
        $dir = array_splice($dir, 2);

        for ($i = 0, $quantity = count($dir); $i < $quantity; $i++) {
            $name = '\\application\providers\\' . explode('.', $dir[$i])[0];
            $entity = new $name();
            $entity->register($di);
            $entity->boot($di);
        }
    }
    function includeApplicationConfig($filename, $context = null) {
        if(file_exists(__DIR_APP__ . $filename))
            return require_once __DIR_APP__ . $filename;
    }
}
