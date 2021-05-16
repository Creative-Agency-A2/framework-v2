<?php

namespace framework\libraries\exception\handler;

use Throwable;

class exceptionHandler implements \framework\libraries\exception\interfaces\exceptionHandlerInterface {

    private $exception = [];
    private $logger;

    public function __construct(\framework\libraries\logger\logger $logger){
        $this->logger = $logger;
    }

    public function handler(Throwable $exception){
        restore_error_handler();
        restore_exception_handler();
        $this->report($exception);
    }

    public function report(Throwable $exception){

        $this->exception['title'] 	= get_class($exception);
		$this->exception['message'] = $exception->getMessage();
		$this->exception['file'] 	= $exception->getFile();
		$this->exception['line'] 	= $exception->getLine();
		$this->exception['trace'] 	= $exception->getTraceAsString();
        $this->exception['ex'] 		= $exception;
        
        $this->render();
    }

    public function render(){
        $context = $this->exception;
        if (__EX_LOG__){
            unset($context['title']);
            $this->logger->critical($this->exception['title'], $context);
        } else {
            header('Content-type: text/html');
            ob_start();
            include_once __DIR_LIBRARIES__ . 'exception/templates/exception.php';
            echo ob_get_clean();
        }
    }

}