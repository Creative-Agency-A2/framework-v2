<?php

namespace framework\libraries\storage\drivers;

class local implements \framework\libraries\storage\StorageInterface {

    private $data = [];

    public function __construct(){}
    public function __clone(){}

    /**
     * Получить значение из хранилища
     *
     * @param string $key
     * @return mixed
     */
    public function get(string $key){
        if ($this->has($key)){
            return $this->data[$key];
        }
    }

    /**
     * Установить значение в хранилище
     *
     * @param string $key
     * @param mixed $value
     * @param boolean $strict
     * @return void
     */
    public function set(string $key, $value,bool $strict = false){
        if ($strict && $this->has($key)) return false;
        $this->data[$key] = $value;
    }

    /**
     * Проверить, существет ли данные в хранилище
     *
     * @param string $key
     * @return boolean
     */
    public function has(string $key){
        return isset($this->data[$key]);
    }

    /**
     * Удалить значение по ключу
     *
     * @param string $key
     * @return void
     */
    public function unset(string $key){
        if ($this->has($key)){
            unset($this->data[$key]);
        }
    }

    /**
     * Очистить хранилище
     *
     * @return void
     */
    public function clean(){
        $this->data = [];
    }

    /**
     * Получить группу значений или значение из группы
     *
     * @param string $groupKey
     * @param string $key
     * @return mixed
     */
    public function getGroup(string $groupKey, string $key = ''){
        if ($key !== '' && isset($this->data[$groupKey][$key])){
            return $this->data[$groupKey][$key];
        }
        if (isset($this->data[$groupKey])){
            return $this->data[groupKey];
        }
        return null;
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
        if ($key !== ''){
            if ($strict && isset($this->data[$groupKey][$key])) return false;
            $this->data[$groupKey][$key] = $value;
        }
        if ($strict && isset($this->data[$groupKey])) return false;
        if ($key == '' && !is_array($value)) return false;
        $this->data[$groupKey] = $value;
    }

}