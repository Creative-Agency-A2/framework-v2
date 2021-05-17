<?php

namespace framework\libraries\storage\drivers;

class redis implements \framework\libraries\storage\StorageInterface {

    private $data = [];

    /**
     * Undocumented variable
     *
     * @var \Redis
     */
    private $instance;

    public function __construct(){
        $this->instance = new \Redis();
        $this->instance->connect(STORAGE_REDIS_HOST, STORAGE_REDIS_PORT);
    }

    public function setName($path_to_file, $path){}

    /**
     * Получить значение из хранилища
     *
     * @param string $key
     * @return mixed
     */
    public function get(string $key){
        return $this->instance->get($key);
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
        $this->instance->set($key, $value);
    }

    /**
     * Проверить, существет ли данные в хранилище
     *
     * @param string $key
     * @return boolean
     */
    public function has(string $key){
       return $this->instance->exists($key) !== 0;
    }

    /**
     * Удалить значение по ключу
     *
     * @param string $key
     * @return void
     */
    public function unset(string $key){
        if ($this->has($key)){
            $this->instance->del($key);
        }
    }

    /**
     * Очистить хранилище
     *
     * @return void
     */
    public function clean(){
        $this->instance->flushDb();
    }

    /**
     * Получить группу значений или значение из группы
     *
     * @param string $groupKey
     * @param string $key
     * @return mixed
     */
    public function getGroup(string $groupKey, string $key = ''){
        if ($this->has($groupKey)){
            $group = $this->get($groupKey);
            $group_array = json_decode($group, true);
            if ($key !== '' && !is_null($group_array) && isset($group_array[$key])){
                return $group_array[$key];
            }

            return is_null($group_array) ? $group : $group_array;
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
        if ($this->has($groupKey)){
            $group = $this->get($groupKey);
            $group_array = json_decode($group, true);
            if ($key !== ''){
                if (!is_null($group_array)){
                    if ($strict && isset($group_array[$key])) return false;
                    $group_array[$key] = $value;
                    $this->set($groupKey, json_encode($group_array));
                } else {
                    $this->set($groupKey, json_encode([$key => $value]));
                }
            }
            if ($key == '' && is_array($value)){
                $this->set($groupKey, json_encode($value));
            }
        }
    }

}