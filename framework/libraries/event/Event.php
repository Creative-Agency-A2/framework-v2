<?php

namespace framework\libraries\event;

class Event {

    public $subject;

    public function __construct()
    {
        $this->subject = new \framework\libraries\event\Subject();
    }

    /**
     * Добавить новую группу наблюдателей
     *
     * @param string $event
     * @return void
     */
    public function initEventGroup(string $event = '*') : void
    {
        $this->subject->initEventGroup($event);
    }

    /**
     * Получить наблюдателей по названию группы
     *
     * @param string $event
     * @return array
     */
    public function getEventObservers(string $event = '*') : array
    {
        return $this->subject->getEventObservers($event);
    }

    /**
     * Присоединить наблюдателя
     *
     * @param \SplObserver|Closure $observer
     * @param string $event
     * @return int
     */
    public function attach($observer, string $event = '*') : int
    {
        return $this->subject->attach($observer, $event);
    }

    /**
     * Отсоединить наблюдателя
     *
     * @param string $event
     * @param int $index
     * @param \SplObserver|null $observer
     * @return void
     */
    public function detach(string $event = "*", int $index = 0, $observer = null) : void
    {
        $this->subject->detach($event, $index, $observer);
    }

    /**
     * Уведомить наблюдателя
     *
     * @param string $event
     * @param mixed $data
     * @return void
     */
    public function notify(string $event = "*", $data = null) : void
    {
        $this->subject->notify($event, $data);
    }
    
}