<?php

namespace framework\libraries\logger\handler;

abstract class abstractHandler extends handler {

    /**
     * Уровень записи
     *
     * @var int
     */
    protected $level = \framework\libraries\logger\logger::DEBUG;

    /**
     * Могут ли обрабатываемые сообщения всплыть в стеке
     *
     * @var boolean
     */
    protected $bubble = true;

    public function __construct($level = \framework\libraries\logger\logger::DEBUG, bool $bubble = true)
    {
        $this->setLevel($level);
        $this->bubble = $bubble;
    }

    /**
     * Базовая проверка обработчика
     * Если уровень предупреждения больше (критичнее), то вернет true
     *
     * @param array $record
     * @return boolean
     */
    public function isHandling(array $record) : bool
    {
        return $record['level'] >= $this->level;
    }

    /**
     * Установить уровень обработки ошибки
     *
     * @param [type] $level
     * @return self
     */
    public function setLevel($level) : self
    {
        $this->level = \framework\libraries\logger\logger::toLevel($level);
        return $this;
    }

    /**
     * Получить уровень обработки ошибки
     *
     * @return integer
     */
    public function getLevel() : int
    {
        return $this->level;
    }

    /**
     * Указать формат работы всплытия
     *
     * @param boolean $bubble
     * @return self
     */
    public function setBubble(bool $bubble) : self 
    {
        $this->bubble = $bubble;
        return $this;
    }

    public function reset()
    {

    }

}