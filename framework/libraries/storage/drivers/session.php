<?php

namespace framework\libraries\storage\drivers;

class session implements \framework\libraries\storage\StorageInterface {

    private $session_ID = null;
    private $data = [];
    private $storage;

    public function __construct($storage){
        $this->storage = $storage;
        if (!isset($_COOKIE['session_id'])){
            if (function_exists('random_bytes')) {
                $session_ID = substr(bin2hex(random_bytes(52)), 0, 52);
            } else {
                $session_ID = substr(bin2hex(openssl_random_pseudo_bytes(52)), 0, 52);
            }
            $this->session_ID = $session_ID;
            $cookie_params = [
                'expires' => time()+(3600 * 24),
                'path' => '/'
            ];
            $cookie_params['Secure'] = true;
            $cookie_params['SameSite'] = 'None';
            setcookie('session_id', $session_ID, $cookie_params);
        } else {
            $this->session_ID = $_COOKIE['session_id'];
        }
        
        $this->storage->setName($this->session_ID, '/sessions/');
        $data = $this->storage->get($this->session_ID);
        $this->data = ($data) ? json_decode($data, true) : [];
        
    }
    public function getId()
    {
        return $this->session_ID;
    }
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
            return $this->data[$groupKey];
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

    function __destruct()
    {
        $this->storage->set($this->session_ID, json_encode($this->data));
        unset($this->storage);
    }

}