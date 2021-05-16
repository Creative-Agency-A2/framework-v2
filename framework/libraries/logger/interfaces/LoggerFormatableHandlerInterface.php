<?php
/**
 * Интерфейс для логгеров, имеющих форматтеры
 */
namespace framework\libraries\logger\interfaces;

interface LoggerFormatableHandlerInterface {
    
    /**
     * Установить форматтер
     *
     * @param LoggerFormatterInterface $formatter
     * @return LoggerHandlerInterface
     */
    public function setFormatter(LoggerFormatterInterface $formatter) : LoggerHandlerInterface;

    /**
     * Получить форматтер
     *
     * @return LoggerFormatterInterface
     */
    public function getFormatter() : LoggerFormatterInterface;

}