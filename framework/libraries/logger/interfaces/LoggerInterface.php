<?php

namespace framework\libraries\logger\interfaces;

interface LoggerInterface {

    /**
     * Система не работает
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function emergency(string $message, array $context = []);

    /**
     * Действие, которое должно быть принято немедленно
     * Например: загрузка сайта или базы данных недоступна
     * Нужно срочно оповестить администратора, чтобы он восстановил работоспособность
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function alert(string $message, array $context = []);

    /**
     * Критическая ошибка
     * Например: недоступен компонент или непредвиденное исключение
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function critical(string $message, array $context = []);

    /**
     * Ошибки, которые не требуют немедленного вмешательства, но которые должны зарегистрироваться и контролироваться
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function error(string $message, array $context = []);

    /**
     * Исключительные случаи, которые не являются ошибками
     * Например: использование устаревшего API и других нежелательных вещей, которые необязательно вредят
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function warning(string $message, array $context = []);

    /**
     * Нормальные, но значимые события, которые не влияют на работоспособность, но должны контролироваться
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function notice(string $message, array $context = []);

    /**
     * События информационного характера
     * Например: пользователь зашел в систему, журнал запросов SQL и т.д.
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function info(string $message, array $context = []);

    /**
     * Подробная информация об отладке
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function debug(string $message, array $context = []);

    /**
     * Запись с произвольным уровнем
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     * @return void
     */
    public function log($level, string $message, array $context = []);

}