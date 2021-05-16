<?php
/**
 * Интерфейс для обработчика исключений
 */
namespace framework\libraries\exception\interfaces;

use Throwable;

interface exceptionHandlerInterface {

    /**
     * Обработка исключений
     *
     * @param Throwable $e
     * @return void
     */
    public function handler(Throwable $e);

    /**
     * Формирование данных для отправки
     *
     * @param Throwable $e
     * @return void
     */
    public function report(Throwable $e);

    /**
     * Отправка данных исключения (на экран, в лог и т.д.)
     *
     * @return void
     */
    public function render();
    
}