<?php
/**
 * Интерфейс для описания обработчиков с процессорами обработок
 */
namespace framework\libraries\logger\interfaces;

interface LoggerProcessableHandlerInterface {

    /**
     * Добавляем процессор в стек
     *
     * @param callable $callback
     * @return \framework\libraries\logger\interfaces\LoggerHandlerInterface
     */
    public function pushProcessor(callable $callback) : LoggerHandlerInterface;

    /**
     * Удаляет процессор из стека и возвращает его
     *
     * @return callable
     */
    public function popProcessor() : callable;

}