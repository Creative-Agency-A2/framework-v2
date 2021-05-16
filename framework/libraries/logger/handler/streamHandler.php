<?php
/**
 * Сохранение лога в любой потоковый ресурс
 * Можно использовать локальные, удаленные файлы или php://stderr
 */
namespace framework\libraries\logger\handler;

use \framework\libraries\logger;
use \framework\libraries\logger\utils;

class streamHandler extends abstractProcessingHandler {
    
    /**
     * Ресурс, в который записывается логи
     *
     * @var resourse
     */
    protected $stream;

    /**
     * Путь до удаленного или локального файла
     *
     * @var string
     */
    protected $url;

    /**
     * Сообщение об ошибке
     *
     * @var ?string
     */
    private $errorMessage;

    /**
     * Права доступа к файлу
     *
     * @var ?int
     */
    protected $filePermission;

    /**
     * Блокировать ли журнал перед записью
     *
     * @var bool
     */
    protected $unsetLocking;

    /**
     * Флаг, который сообщает о том, была ли создана директория
     *
     * @var ?bool
     */
    private $dirCreated;

    /**
     *
     * @param resource|string $stream - если подается неправильный тип, то будет инициировано исключение
     * @param string|int $level - минимальный уровень логгирования, при котором сработает данный обработчик
     * @param boolean $bubble - нужно ли всплытие обработчиков
     * @param integer|null $filePermission - права доступа к локальному файлу
     * @param boolean $unsetLocking - следует ли блокировать лог перед тем, как начать запись
     */
    public function __construct($stream, $level = logger::DEBUG, bool $bubble = true, ?int $filePermission = null, bool $unsetLocking = false)
    {
        parent::__construct($level, $bubble);
        if (is_resource($stream)){
            $this->stream = $stream;
        } elseif (is_string($stream)){
            $this->url = utils::canonicalizePath($stream);
        } else {
            throw new \InvalidArgumentException('A stream must either be a resource or a string.');
        }
        $this->filePermission = $filePermission;
        $this->unsetLocking = $unsetLocking;
    }

    public function close(): void
    {
        if ($this->url && is_resource($this->stream)) {
            fclose($this->stream);
        }
        $this->stream = null;
        $this->dirCreated = null;
    }

    /**
     * Вернуть текущий активный поток, если он открыт
     *
     * @return resource|null
     */
    public function getStream()
    {
        return $this->stream;
    }

    /**
     * Вернуть URL-адрес потока, если он был настроен с URL-адресом, а не с активным ресурсом
     *
     * @return string|null
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * Записать лог
     */
    protected function write(array $record): void
    {
        if (!is_resource($this->stream)) {
            if (null === $this->url || '' === $this->url) {
                throw new \LogicException('Missing stream url, the stream can not be opened. This may be caused by a premature call to close().');
            }

            // Создаем директорию
            $this->createDir();

            $this->errorMessage = null;
            set_error_handler([$this, 'customErrorHandler']);
            $this->stream = fopen($this->url, 'a');
            if ($this->filePermission !== null) {
                @chmod($this->url, $this->filePermission);
            }
            restore_error_handler();
            if (!is_resource($this->stream)) {
                $this->stream = null;
                throw new \UnexpectedValueException(sprintf('The stream or file "%s" could not be opened in append mode: '.$this->errorMessage, $this->url));
            }
        }

        if ($this->unsetLocking) {
            flock($this->stream, LOCK_EX);
        }

        $this->streamWrite($this->stream, $record);

        if ($this->unsetLocking) {
            flock($this->stream, LOCK_UN);
        }
    }

    /**
     * Записываем лог в журнал
     * @param resource $stream
     * @param array    $record
     */
    protected function streamWrite($stream, array $record): void
    {
        fwrite($stream, (string) $record['formatted']);
    }

    /**
     * Локальный обработчик ошибок
     * Используется при попытке создать директорию и при записи в журнал
     *
     * @param [type] $code
     * @param [type] $msg
     * @return boolean
     */
    private function customErrorHandler($code, $msg): bool
    {
        $this->errorMessage = preg_replace('{^(fopen|mkdir)\(.*?\): }', '', $msg);

        return true;
    }

    /**
     * Получить директорию потока
     * В случае, если это локальный файл - возвращает директорию данного файла 
     *
     * @param string $stream
     * @return string|null
     */
    private function getDirFromStream(string $stream): ?string
    {
        $pos = strpos($stream, '://');
        if ($pos === false) {
            return dirname($stream);
        }

        if ('file://' === substr($stream, 0, 7)) {
            return dirname(substr($stream, 7));
        }

        return null;
    }

    /**
     * Создаем директорию для лога
     *
     * @return void
     */
    private function createDir(): void
    {
        // Не создаем директорию, если это уже сделано
        if ($this->dirCreated) {
            return;
        }

        // Получаем директорию
        $dir = $this->getDirFromStream($this->url);
        
        // Если директория не создана - создаем ее
        if (null !== $dir && !is_dir($dir)) {
            $this->errorMessage = null;
            set_error_handler([$this, 'customErrorHandler']);
            $status = mkdir($dir, 0777, true);
            restore_error_handler();
            if (false === $status && !is_dir($dir)) {
                throw new \UnexpectedValueException(sprintf('There is no existing directory at "%s" and it could not be created: '.$this->errorMessage, $dir));
            }
        }
        $this->dirCreated = true;
    }

}