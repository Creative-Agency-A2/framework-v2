<?php

namespace framework\framework\libraries\interfaces;

interface StreamInterface {

    public function __toString() : string;

    /**
     * Закрыть поток
     *
     * @return void
     */
    public function close();

    /**
     * Отделить ресурс от потока
     *
     * @return resource|null
     */
    public function detach();

    /**
     * Получить размер потока, если его возможно определить
     * 
     * @return int|null
     */
    public function getSize();

    /**
     * Получить текущую позицию указателя при чтении/записи
     *
     * @return integer
     * @throws \RuntimeException
     */
    public function tell() : int;

    /**
     * Возвращает TRUE, если наступил конец потока
     *
     * @return boolean
     */
    public function eof() : bool;

    /**
     * Доступен ли поток для поиска
     *
     * @return boolean
     */
    public function isSeekable() : bool;

    /**
     * Переместить указатель
     *
     * @param int $offset
     * @param int $whence
     * @return void
     * @throws \RuntimeException
     */
    public function seek($offset, $whence = SEEK_SET);

    /**
     * Переместить указатель на начало
     * Аналогичен seek(0)
     *
     * @return void
     */
    public function rewind();

    /**
     * Доступен ли поток для записи
     *
     * @return boolean
     */
    public function isWritable() : bool;

    /**
     * Записать строку в поток
     * Возвращает кол-во байт, записанных в поток
     *
     * @param string $string
     * @return integer
     */
    public function write(string $string) : int;

    /**
     * Доступен ли поток для чтения
     *
     * @return boolean
     */
    public function isReadable() : bool;

    /**
     * Прочитать указанное кол-во байт с потока
     *
     * @param integer $length
     * @return string
     * @throws \RuntimeException
     */
    public function read(int $length) : string;

    /**
     * Получить оставшиеся данные из потока
     *
     * @return string
     * @throws \RuntimeException
     */
    public function getContents() : string;

    /**
     * Получить метаданные из потока
     *
     * @param string $key
     * @return array|mixed|null
     */
    public function getMetadata(string $key);


}