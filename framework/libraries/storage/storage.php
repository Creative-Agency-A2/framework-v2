<?php

namespace framework\libraries\storage;

class storage implements StorageInterface {

    private $instance;

    public function __construct($storage){
        $this->instance = $storage;
    }
    public function getId()
    {
        return $this->instance->getId();
    }

    public function get(string $key){
        return $this->instance->get($key);
    }

    public function set(string $key, $value, bool $strict = false){
        $this->instance->set($key, $value, $strict);
    }

    public function has(string $key){
        return $this->instance->has($key);
    }

    /**
     * Удалить значение по ключу
     *
     * @param string $key
     * @return void
     */
    public function unset(string $key){
        $this->instance->unset($key);
    }

    /**
     * Очистить хранилище
     *
     * @return void
     */
    public function clean(){
        $this->instance->clean();
    }

    /**
     * Получить группу значений или значение из группы
     *
     * @param string $groupKey
     * @param string $key
     * @return mixed
     */
    public function getGroup(string $groupKey, string $key = ''){
        return $this->instance->getGroup($groupKey, $key);
    }

    /**
     * Установить значение в группу
     *
     * @param string $groupKey
     * @param mixed $value
     * @param string $key
     * @param boolean $strict
     * @return void
     */
    public function setGroup(string $groupKey, $value, string $key = '', bool $strict = false){
        $this->instance->setGroup($groupKey, $value, $key, $strict);
    }
}