<?php
/**
 * Обработчик исключений и ошибок
 */
namespace framework\libraries\exception;

use Exception;
use ErrorException;
use framework\libraries\exception\FatalErrorException;
use framework\libraries\exception\FatalThrowableError;

class ExceptionHandler {

    protected $handler = null;

    public function __construct($exceptionHandler)
    {
        $this->handler = $exceptionHandler;
    }

    /**
     * Конвертируем PHP ошибку в ErrorException
     *
     * @param [type] $level
     * @param [type] $message
     * @param string $file
     * @param integer $line
     * @param array $context
     * @return void
     */
    public function handleError($level, $message, $file = '', $line = 0, $context = [])
    {
        if (error_reporting() & $level) {
            throw new ErrorException($message, 0, $level, $file, $line);
        }
    }

    public function handleException($e){
        if (! $e instanceof Exception){
            $e = new FatalThrowableError($e);
        }
        $this->getExceptionHandler()->report($e);
        $this->getExceptionHandler()->render();
    }

    public function getExceptionHandler()
    {
        return $this->handler;
    }

    public function handleShutdown()
    {
        if (! is_null($error = error_get_last()) && $this->isFatal($error['type'])) {
            $this->handleException($this->fatalExceptionFromError($error, 0));
        }
    }

    protected function fatalExceptionFromError(array $error, $traceOffset = null)
    {
        return new FatalErrorException(
            $error['message'], $error['type'], 0, $error['file'], $error['line'], $traceOffset
        );
    }

    protected function isFatal($type)
    {
        return in_array($type, [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE]);
    }

    public function init()
    {

        // Добавляем в журнал все ошибки
        error_reporting(-1);

        // Обработчик ошибок
        set_error_handler([$this, 'handleError']);

        // Обработчик исключений
        set_exception_handler([$this, 'handleException']);

        // Обработчик критических исключений
        register_shutdown_function([$this, 'handleShutdown']);

        if (__DEBUG__){
            ini_set('display_errors', 'Off');
        }
    }

}