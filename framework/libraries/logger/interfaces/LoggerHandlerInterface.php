<?php

namespace framework\libraries\logger\interfaces;

interface LoggerHandlerInterface {

    /**
     * Проверяет, будет ли данная запись обрабатываться этим обработчиком
     *
     * @param array $record
     * @return boolean
     */
    public function isHandling(array $record) : bool;

    /**
     * Обработка записи
     * Все записи передаются в данный метод, и те записи, которые не обрабатываются данным обработчиком должны быть сброшены
     * Если возвращается false, то выполняется следующий обработчик до того момента, пока метод не вернет true
     *
     * @param array $record
     * @return bool
     */
    public function handle(array $record) : bool;

    /**
     * Пакетная обработка записей
     *
     * @param array $records
     * @return void
     */
    public function handleBatch(array $records) : void;

    /**
     * Закрывает обработчик
     *
     * @return void
     */
    public function close() : void;


}