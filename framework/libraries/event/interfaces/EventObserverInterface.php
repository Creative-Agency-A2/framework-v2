<?php

namespace framework\libraries\event\interfaces;

interface EventObserverInterface {

    /**
     * Реакция наблюдателя на событие
     *
     * @param string $event
     * @param array|null $data
     * @return void
     */
    public function update(string $event, $data) : void;
}