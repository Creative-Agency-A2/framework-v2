<?php

namespace framework\libraries\event\interfaces;

interface EventSubjectInterface {

    /**
     * Добавление наблюдателя
     *
     * @param Clousure|EventObserverInterface $observer
     * @param string $event
     * @return integer
     */
    public function attach($observer, string $event) : int;

    /**
     * Удаление наблюдателя
     *
     * @param string $event
     * @param integer $index
     * @param EventObserverInterface $observer
     * @return void
     */
    public function detach(string $event, int $index, $observer) : void;

    /**
     * Отправление события всем наблюдателям
     *
     * @param string $event
     * @param array|null $data
     * @return void
     */
    public function notify(string $event, $data) : void;
}
