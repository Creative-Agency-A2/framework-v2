<?php
/**
 * Интерфейс для форматтеров
 * Форматтеры - это вспомогательные классы, которые форматируют записи логов в тот или иной формат
 */
namespace framework\libraries\logger\interfaces;

interface LoggerFormatterInterface {

    /**
     * Форматировать запись
     *
     * @param array $record
     * @return void
     */
    public function format(array $record);

    /**
     * Пакетно форматировать записи
     *
     * @param array $records
     * @return void
     */
    public function formatBatch(array $records);
}