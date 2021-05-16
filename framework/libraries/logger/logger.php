<?php

namespace framework\libraries\logger;

use \framework\libraries\logger\interfaces\LoggerHandlerInterface;
use \framework\libraries\logger\interfaces\LoggerResettableInterface;

class logger implements interfaces\LoggerInterface {

    public const DEBUG = 100;
    public const INFO = 200;
    public const NOTICE = 250;
    public const WARNING = 300;
    public const ERROR = 400;
    public const CRITICAL = 500;
    public const ALERT = 550;
    public const EMERGENCY = 600;

    protected static $levels = [
        self::DEBUG     => 'DEBUG',
        self::INFO      => 'INFO',
        self::NOTICE    => 'NOTICE',
        self::WARNING   => 'WARNING',
        self::ERROR     => 'ERROR',
        self::CRITICAL  => 'CRITICAL',
        self::ALERT     => 'ALERT',
        self::EMERGENCY => 'EMERGENCY',
    ];

    /**
     *
     * @var string
     */
    protected $name;

    /**
     * Массив обработчиков
     *
     * @var array
     */
    protected $handlers = [];

    
    /**
     * Процессоры обработчиков
     *
     * @var array
     */
    protected $processors = [];

    /**
     * Данный флаг используется в microtime($microsecondTimestamps)
     * 
     * @var bool
     */
    protected $microsecondTimestamps = true;

    /**
     * Часовой пояс
     * 
     * @var DateTimeZone
     */
    protected $timezone;

    /**
     * Обработчик исключений
     * 
     * @var callable|null
     */
    protected $exceptionHandler;

    public function __construct(string $name, array $handlers = [], array $processors = [], ?\DateTimeZone $timezone = null)
    {
        $this->name = $name;
        $this->setHandlers($handlers);
        $this->processors = $processors;
        $this->timezone = $timezone ?: new \DateTimeZone(date_default_timezone_get()) ?: 'UTC';
    }

    /**
     * Получить название логгера
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Вернуть новый логгер с измененным именем
     * 
     * @param string $name
     */
    public function withName(string $name): self
    {
        $new = clone $this;
        $new->name = $name;

        return $new;
    }

    /**
     * Добавить обработчик в стек
     * 
     * @param LoggerHandlerInterface $handler
     */
    public function pushHandler(LoggerHandlerInterface $handler): self
    {
        array_unshift($this->handlers, $handler);

        return $this;
    }

    /**
     * Извлечь обработчик из стека
     *
     * @throws \LogicException Если стек обработчиков пуст
     * @return \framework\interfaces\LoggerHandlerInterface
     */
    public function popHandler(): LoggerHandlerInterface
    {
        if (!$this->handlers) {
            throw new \LogicException('You tried to pop from an empty handler stack.');
        }

        return array_shift($this->handlers);
    }

    /**
     * Заменить все обработчики
     *
     * @param \framework\interfaces\LoggerHandlerInterface[] $handlers
     * 
     * @return self
     */
    public function setHandlers(array $handlers)
    {
        $this->handlers = [];
        foreach (array_reverse($handlers) as $handler) {
            $this->pushHandler($handler);
        }

        return $this;
    }

    /**
     * Получить все обработчики
     * 
     * @return \framework\interfaces\LoggerHandlerInterface[]
     */
    public function getHandlers(): array
    {
        return $this->handlers;
    }

    /**
     * Добавляет процессор в стек
     */
    public function pushProcessor(callable $callback): self
    {
        array_unshift($this->processors, $callback);

        return $this;
    }

    /**
     * Удаляет процессор из стека и возвращает его
     *
     * @throws \LogicException Если стек процессоров пуст
     * @return callable
     */
    public function popProcessor(): callable
    {
        if (!$this->processors) {
            throw new \LogicException('You tried to pop from an empty processor stack.');
        }

        return array_shift($this->processors);
    }

    /**
     * Получить все процессоры
     * 
     * @return callable[]
     */
    public function getProcessors(): array
    {
        return $this->processors;
    }

    /**
     * Установить флаг для использования микросекунд
     *
     * @param bool $micro
     */
    public function useMicrosecondTimestamps(bool $micro): void
    {
        $this->microsecondTimestamps = $micro;
    }

    /**
     * Получить название уровня логгирования по номеру
     *
     * @param integer $level
     * @return string
     */
    public function getLevelName(int $level) : string
    {
        return static::$levels[$level];
    }

    /**
     * Запись в журнал
     *
     * @param integer $level
     * @param string $message
     * @param array $context
     * @return boolean
     */
    public function addRecord(int $level, string $message, array $context = []) : bool
    {
        $record = null;
        foreach ($this->handlers as $handler){
            if ($record === null){
                // Пропускаем те обработчики, которые не подходят по уровню срабатывания лога
                if (!$handler->isHandling(['level' => $level])) {
                    continue;
                }
                $levelName = static::getLevelName($level);
                
                $record = [
                    'message' => $message,
                    'context' => $context,
                    'level' => $level,
                    'level_name' => $levelName,
                    'channel' => $this->name,
                    'datetime' => new DateTimeImmutable($this->microsecondTimestamps, $this->timezone),
                    'extra' => [],
                ];

                try {
                    foreach ($this->processors as $processor) {
                        $record = $processor($record);
                    }
                } catch (Throwable $e) {
                    $this->handleException($e, $record);

                    return true;
                }
            }

            // Если всплытие отключено, то после первого обработчика логгер останавливается
            try {
                if (true === $handler->handle($record)) {
                    break;
                }
            } catch (Throwable $e) {
                $this->handleException($e, $record);

                return true;
            }
        }
        return null !== $record;
    }

    /**
     * Закрыть обработчики
     *
     * @return void
     */
    public function close(): void
    {
        foreach ($this->handlers as $handler) {
            $handler->close();
        }
    }

    /**
     * Сброс обработчиков и процессоров
     *
     * @return void
     */
    public function reset(): void
    {
        foreach ($this->handlers as $handler) {
            if ($handler instanceof LoggerResettableInterface) {
                $handler->reset();
            }
        }

        foreach ($this->processors as $processor) {
            if ($processor instanceof LoggerResettableInterface) {
                $processor->reset();
            }
        }
    }

    /**
     * Получить все поддерживаемые уровни ведения логов
     *
     * @return array<string, int>
     */
    public static function getLevels(): array
    {
        return array_flip(static::$levels);
    }

    /**
     * Преобразование уровней PSR-3 в уровни логгера, при необходимости
     *
     * @param  string|int                        $level номер уровня или строка
     * @throws \Psr\Log\InvalidArgumentException Если уровень не определен
     */
    public static function toLevel($level): int
    {
        if (is_string($level)) {
            if (is_numeric($level)) {
                return intval($level);
            }

            // Contains chars of all log levels and avoids using strtoupper() which may have
            // strange results depending on locale (for example, "i" will become "İ" in Turkish locale)
            $upper = strtr($level, 'abcdefgilmnortuwy', 'ABCDEFGILMNORTUWY');
            if (defined(__CLASS__.'::'.$upper)) {
                return constant(__CLASS__ . '::' . $upper);
            }

            throw new \InvalidArgumentException('Level "'.$level.'" is not defined, use one of: '.implode(', ', array_keys(static::$levels)));
        }

        if (!is_int($level)) {
            throw new \InvalidArgumentException('Level "'.var_export($level, true).'" is not defined, use one of: '.implode(', ', array_keys(static::$levels)));
        }

        return $level;
    }

    /**
     * Проверяет, есть ли у логгера обработчик, который слушает данный уровень логов
     * 
     * @param int $level
     * @return bool
     */
    public function isHandling(int $level): bool
    {
        $record = [
            'level' => $level,
        ];

        foreach ($this->handlers as $handler) {
            if ($handler->isHandling($record)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Установить кастомный обработчик исключений
     * Данный обработчик будет вызываться при сбое добавления записи в лог
     * В обработчик исключения будет передано 2 параметра:
     * 1. Данные ошибки
     * 2. Сообщение, которое не получилось записать
     * 
     * @param callable|null $callback
     */
    public function setExceptionHandler(?callable $callback): self
    {
        $this->exceptionHandler = $callback;

        return $this;
    }

    /**
     * Получить кастомный обработчик исключений
     *
     * @return callable|null
     */
    public function getExceptionHandler(): ?callable
    {
        return $this->exceptionHandler;
    }

    /**
     * Добавить запись в журнал с произвольным уровнем
     * Данный метод обеспечивает совместимость с PSR-3
     *
     * @param mixed   $level   The log level
     * @param string  $message The log message
     * @param mixed[] $context The log context
     */
    public function log($level, $message, array $context = []): void
    {
        $level = static::toLevel($level);

        $this->addRecord($level, (string) $message, $context);
    }

    /**
     * Добавляет запись журнала на уровне DEBUG.
     *
     * Данный метод обеспечивает совместимость с PSR-3
     *
     * @param string  $message The log message
     * @param mixed[] $context The log context
     */
    public function debug($message, array $context = []): void
    {
        $this->addRecord(static::DEBUG, (string) $message, $context);
    }

    /**
     * Добавляет запись журнала на уровне INFO.
     *
     * Данный метод обеспечивает совместимость с PSR-3
     *
     * @param string  $message The log message
     * @param mixed[] $context The log context
     */
    public function info($message, array $context = []): void
    {
        $this->addRecord(static::INFO, (string) $message, $context);
    }

    /**
     * Добавляет запись журнала на уровне NOTICE.
     *
     * Данный метод обеспечивает совместимость с PSR-3
     *
     * @param string  $message The log message
     * @param mixed[] $context The log context
     */
    public function notice($message, array $context = []): void
    {
        $this->addRecord(static::NOTICE, (string) $message, $context);
    }

    /**
     * Добавляет запись в журнал на уровне WARNING.
     *
     * Данный метод обеспечивает совместимость с PSR-3
     *
     * @param string  $message The log message
     * @param mixed[] $context The log context
     */
    public function warning($message, array $context = []): void
    {
        $this->addRecord(static::WARNING, (string) $message, $context);
    }

    /**
     * Добавляет запись в журнал на уровне ERROR
     *
     * Данный метод обеспечивает совместимость с PSR-3
     *
     * @param string  $message The log message
     * @param mixed[] $context The log context
     */
    public function error($message, array $context = []): void
    {
        $this->addRecord(static::ERROR, (string) $message, $context);
    }

    /**
     * Добавляет запись в журнал на уровне CRITICAL
     *
     * Данный метод обеспечивает совместимость с PSR-3
     *
     * @param string  $message The log message
     * @param mixed[] $context The log context
     */
    public function critical($message, array $context = []): void
    {
        $this->addRecord(static::CRITICAL, (string) $message, $context);
    }

    /**
     * Добавляет запись в журнал на уровне ALERT
     *
     * Данный метод обеспечивает совместимость с PSR-3
     *
     * @param string  $message The log message
     * @param mixed[] $context The log context
     */
    public function alert($message, array $context = []): void
    {
        $this->addRecord(static::ALERT, (string) $message, $context);
    }

    /**
     * Добавляет запись в журнал на уровне EMERGENCY
     *
     * Данный метод обеспечивает совместимость с PSR-3
     *
     * @param string  $message The log message
     * @param mixed[] $context The log context
     */
    public function emergency($message, array $context = []): void
    {
        $this->addRecord(static::EMERGENCY, (string) $message, $context);
    }

    /**
     * Устанавливает часовой пояс, который будет использоваться для отметки времени в записях журнала.
     * 
     * @param \DateTimeZone $tz
     * @return self
     */
    public function setTimezone(\DateTimeZone $tz): self
    {
        $this->timezone = $tz;

        return $this;
    }

    /**
     * Возвращает часовой пояс, который будет использоваться для отметки времени записей журнала.
     */
    public function getTimezone(): \DateTimeZone
    {
        return $this->timezone;
    }

    /**
     * Делегирует управление исключениями пользовательскому обработчику исключений 
     * или генерирует исключение, если пользовательский обработчик не установлен.
     * 
     * @param \Throwable $e
     * @param array $record
     * 
     * @return void
     */
    protected function handleException(\Throwable $e, array $record): void
    {
        if (!$this->exceptionHandler) {
            throw $e;
        }

        ($this->exceptionHandler)($e, $record);
    }
    
}