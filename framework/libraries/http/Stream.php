<?php

namespace framework\libraries\http;

use framerowk\libraries\http\interfaces\StreamInterface;

class Stream implements StreamInterface {

    private $resource;

    private $stream;

    public function __construct($stream, $mode = 'r')
    {
        $this->stream = $stream;

        if (\is_resource($stream)){
            $this->resource = $stream;
        } elseif (is_string($stream)){
            set_error_handler(function ($errno, $errstr) {
                throw new \InvalidArgumentException(
                    'Для потока предоставлен неправильный путь до источника'
                );
            }, E_WARNING);
            $this->resource = fopen($stream, $mode);
            restore_error_handler();
        } else {
            throw new \InvalidArgumentException("Параметр stream должен быть строкой или ресурсом");
        }
    }

    /**
     * При попытке обратиться к классу, как к строке
     * мы пытаемся считать весь поток с нулевого указателя
     * При неудачной попытке - возвращаем пустую строку
     *
     * @return string
     */
    public function __toString() : string
    {
        if (!$this->isReadable()){
            return '';
        }
        try {
            $this->rewind();
            return $this->getContents();
        } catch (\RuntimeException $th) {
            return '';
        }
    }

    /**
     * Закрыть поток
     *
     * @return void
     */
    public function close()
    {
        if (!$this->resource){
            return;
        }

        $resource = $this->detach();
        fclose($resource);
    }

    /**
     * Отделить ресурс от потока
     *
     * @return resource|null
     */
    public function detach()
    {
        $resource = $this->resource;
        $this->resource = null;
        return $resource;
    }

    /**
     * Получить размер потока, если его возможно определить
     * 
     * @return int|null
     */
    public function getSize()
    {
        if (null === $this->resource){
            return null;
        }

        $stats = fstat($this->resource);
        return $stats['size'];
    }

    /**
     * Получить текущую позицию указателя при чтении/записи
     *
     * @return integer
     * @throws \RuntimeException
     */
    public function tell() : int
    {
        if ( !$this->resource) {
            throw new \RuntimeException('Ресурс не обнаружен; Невозможно получить текущую позицию указателя');
        }

        $result = ftell($this->resource);
        if ( !is_int($result)) {
            throw new \RuntimeException('Произошла ошибка во время выполнения операции tell');
        }

        return $result;
    }

    /**
     * Возвращает TRUE, если наступил конец потока
     *
     * @return boolean
     */
    public function eof() : bool
    {
        if ( !$this->resource) {
            return true;
        }

        return feof($this->resource);
    }

    /**
     * Доступен ли поток для поиска
     *
     * @return boolean
     */
    public function isSeekable() : bool
    {
        if ( !$this->resource ) {
            return false;
        }

        $meta = stream_get_meta_data($this->resource);
        return $meta['seekable'];
    }

    /**
     * Переместить указатель
     *
     * @param int $offset
     * @param int $whence
     * @return void
     * @throws \RuntimeException
     */
    public function seek($offset, $whence = SEEK_SET)
    {
        if (! $this->resource) {
            throw new \RuntimeException('Ресурс не обнаружен; невозможно переместить указатель');
        }
        
        if (! $this->isSeekable()) {
            throw new \RuntimeException('В данном потоке нельзя переместить указатель');
        }

        $result = fseek($this->resource, $offset, $whence);

        if (0 !== $result) {
            throw new \RuntimeException('Ошибка перемещения указателя в потоке');
        }

        return true;
    }

    /**
     * Переместить указатель на начало
     * Аналогичен seek(0)
     *
     * @return void
     */
    public function rewind()
    {
        $this->seek(0);
    }

    /**
     * Доступен ли поток для записи
     *
     * @return boolean
     */
    public function isWritable() : bool
    {
        if (! $this->resource) {
            return false;
        }

        $meta = stream_get_meta_data($this->resource);
        return is_writable($meta['uri']);
    }

    /**
     * Записать строку в поток
     * Возвращает кол-во байт, записанных в поток
     *
     * @param string $string
     * @return integer
     */
    public function write(string $string) : int
    {
        if (! $this->resource) {
            throw new \RuntimeException('Ресурс не обнаружен; запись невозможна');
        }

        $result = fwrite($this->resource, $string);

        if (false === $result) {
            throw new \RuntimeException('Ошибка записи в поток');
        }
        return $result;
    }

    /**
     * Доступен ли поток для чтения
     *
     * @return boolean
     */
    public function isReadable() : bool
    {
        if (! $this->resource) {
            return false;
        }

        $meta = stream_get_meta_data($this->resource);
        $mode = $meta['mode'];

        return (strstr($mode, 'r') || strstr($mode, '+'));
    }

    /**
     * Прочитать указанное кол-во байт с потока
     *
     * @param integer $length
     * @return string
     * @throws \RuntimeException
     */
    public function read(int $length) : string
    {
        if (! $this->resource) {
            throw new \RuntimeException('Ресурс не обнаружен; невозможно прочитать данные');
        }

        if (! $this->isReadable()) {
            throw new \RuntimeException('Поток не доступен для чтения');
        }

        $result = fread($this->resource, $length);

        if (false === $result) {
            throw new \RuntimeException('Ошибка чтения потока');
        }

        return $result;
    }

    /**
     * Получить все данные из потока, начиная с нынешней позиции указателя
     *
     * @return string
     * @throws \RuntimeException
     */
    public function getContents() : string
    {
        if ( !$this->isReadable() ) {
            return '';
        }

        $result = stream_get_contents($this->resource);
        if (false === $result) {
            throw new \RuntimeException('Ошибка чтения данных из потока');
        }
        return $result;
    }

    /**
     * Получить метаданные потока
     *
     * @param string $key
     * @return array|mixed|null
     */
    public function getMetadata(string $key)
    {
        if (null === $key) {
            return stream_get_meta_data($this->resource);
        }

        $metadata = stream_get_meta_data($this->resource);
        if (! array_key_exists($key, $metadata)) {
            return null;
        }

        return $metadata[$key];
    }

}