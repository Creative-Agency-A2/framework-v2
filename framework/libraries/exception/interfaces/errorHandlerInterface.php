<?php
/**
 * Интерфейс для обработчика пользовательских ошибок
 * Например, при вызове trigger_error()
 */
namespace framework\libraries\exception\interfaces;

interface errorHandlerInterface {

    /**
     * Обработка исключений
     *
     * @param Throwable $e
     * @return void
     */
    public function handler($code, $message, $file, $line);

    /**
     * Формирование данных для отправки
     *
     * @param Throwable $e
     * @return void
     */
    public function report($code, $message, $file, $line);

    /**
     * Отправка данных исключения (на экран, в лог и т.д.)
     *
     * @return void
     */
    public function render();
    
}