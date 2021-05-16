<?php

namespace framework\libraries\event;

use framework\libraries\event\interfaces\EventObserverInterface as EventObserverInterface;

class Subject implements \framework\libraries\event\interfaces\EventSubjectInterface {

    public $observers = [];

    public function __construct()
    {
        $this->observers['*'] = [];
    }

    /**
     * Добавить новую группу наблюдателей
     *
     * @param string $event
     * @return void
     */
    public function initEventGroup(string $event = "*") : void
    {
        if (!isset($this->observers[$event])) {
            $this->observers[$event] = [];
        }
    }

    /**
     * Получить наблюдателей по названию группы
     *
     * @param string $event
     * @return array
     */
    public function getEventObservers(string $event = "*") : array
    {
        $this->initEventGroup($event);
        $group = $this->observers[$event];

        if ($event !== '*'){
            $all = $this->observers["*"];
        } else {
            $all = [];
        }

        return array_merge($group, $all);
    }

    /**
     * Присоединить наблюдателя
     *
     * @param EventObserverInterface|Closure $observer
     * @param string $event
     * @return int
     */
    public function attach($observer, string $event = "*") : int
    {
        $this->initEventGroup($event);
        $this->observers[$event][] = $observer;
        return count($this->observers[$event]) - 1;
    }

    /**
     * Отсоединить наблюдателя
     *
     * @param string $event
     * @param int $index
     * @param EventObserverInterface|null $observer
     * @return void
     */
    public function detach(string $event = "*", int $index = 0, $observer = null) : void
    {
        if ($index !== 0 && isset($this->observers[$event][$index])){
            unset($this->observers[$event][$index]);
        } elseif ($observer instanceof EventObserverInterface){
            foreach ($this->getEventObservers($event) as $key => $s) {
                if ($s === $observer) {
                    unset($this->observers[$event][$key]);
                }
            }
        }
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
        foreach ($this->getEventObservers($event) as $observer) {
            if ($observer instanceof \Closure){
                \call_user_func($observer, $data);
            }
            if ($observer instanceof EventObserverInterface){
                $observer->update($event, $data);
            }
        }

    }

}