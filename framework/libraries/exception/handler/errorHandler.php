<?php

namespace framework\libraries\exception\handler;

class errorHandler implements \framework\libraries\exception\interfaces\errorHandlerInterface {

    /**
     * @var array 
     */
    private $error;
    /**
     * @var \framework\libraries\logger\logger 
     */
    private $logger;

    public function __construct(\framework\libraries\logger\logger $logger){
        $this->logger = $logger;
    }

    /**
     * Хуй его знает 
     *
     * @param int $code
     * @param string $message
     * @param string $file
     * @param int $line
     * @return void
     */
    public function handler(int $code, string $message, string $file, int $line){
        if ($code === error_reporting()) {
            restore_error_handler();
            restore_exception_handler();
            try {
                $this->report($code, $message, $file, $line);
            } catch ( \Throwable $e ) {
                $exception_handler = new exceptionHandler($this->logger);
                $exception_handler->handler($e);
            }
        }
    }

    /**
     *Хуй его знает 
     *
     * @param int $code
     * @param string $message
     * @param string $file
     * @param int $line
     * @return void
     */
    public function report(int $code, string $message, string $file, int $line){
        $this->error['title'] 	= 'PHP Error [' . $code . ']';
		$this->error['message'] = $message . '(' . $file . ':' . $line . ')';
		$this->error['file'] 	= $file;
        $this->error['line'] 	= $line;
        
        $trace = debug_backtrace();

		if(count($trace) > 3) {
		    $trace = array_slice($trace, 3);
		}

        $trace_str = '';
        foreach($trace as $i => $t){
            if (!isset($t['file']))
                $t['file'] = 'unknown';
            
            if (!isset($t['line']))
                $t['line'] = 0;
            
            if (!isset($t['function']))
                $t['function'] = 'unknown';
            $trace_str .= "#$i {$t['file']}({$t['line']}): ";
            if (isset($t['object']) && is_object($t['object']))
            $trace_str .= get_class($t['object']).'->';
            $trace_str .= "{$t['function']}()\n";
        }

		$this->error['trace'] 	= $trace_str;
        
        $this->render();
    }

    /**
     * Хуй его знает 
     *
     * @return void
     */
    public function render(){
        $context = $this->error;
        if (__EX_LOG__){
            unset($context['title']);
            $this->logger->error($this->error['title'], $context);
        } else {
            ob_start();
            include_once __DIR_LIBRARIES__ . 'exception/templates/error.php';
            echo ob_get_clean();
        }
    }
}