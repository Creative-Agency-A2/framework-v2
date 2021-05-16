<?php
/**
 * Базовый класс обработчика
 * Реализация по умолчанию методов handleBatch и close
 */
namespace framework\libraries\logger\handler;

use framework\libraries\logger\interfaces\LoggerHandlerInterface;

abstract class handler implements LoggerHandlerInterface {

    /**
     * Базовая реализация пакетной обработки записей
     *
     * @param array $records
     * @return void
     */
    public function handleBatch(array $records) : void
    {
        foreach ($records as $record){
            $this->handler($record);
        }
    }

    /**
     * Пустой метод как заглушка
     *
     * @return void
     */
    public function close() : void
    {

    }

    /**
     * Пытаемся закрыть обработчик, при уничтожении класса
     */
    public function __destruct()
    {
        try {
            $this->close();
        } catch (\Throwable $th) {
            
        }
    }

    /**
     * Метод для подготовки данных перед сериализацией
     *
     * @return array
     */
    public function __sleep() : array
    {
        $this->close();
        return \array_keys(\get_object_vars($this));
    }

}