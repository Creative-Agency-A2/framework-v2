<?php

namespace framework\libraries\storage\drivers;

class database implements \framework\libraries\storage\StorageInterface {
    
    protected $instances = [];
    protected $parameters = [];

    private $data = [];
    private $session_id = null;
    private $db_connect = null;

    public function __construct($db_connect, $db_config)
    {
        $this->db_connect = $db_connect;
        $this->db_config = $db_config;

        $session_table = $this->db_connect->query('SHOW TABLES FROM `'.$this->db_config['database'].'` LIKE "sessions";')->row;
        if(empty($session_table)) {
            $this->db_connect->query("
            CREATE TABLE `sessions` (
                `id` int NOT NULL,
                `ip` varchar(24) DEFAULT NULL,
                `session_id` varchar(255) DEFAULT NULL,
                `data` text CHARACTER SET utf8 COLLATE utf8_general_ci,
                `date_added` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `date_updated` datetime DEFAULT NULL
              ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
              
              $this->db_connect->query("ALTER TABLE `sessions`
                ADD PRIMARY KEY (`id`);");
              $this->db_connect->query("ALTER TABLE `sessions`
                MODIFY `id` int NOT NULL AUTO_INCREMENT;");
             $this->db_connect->query("COMMIT;");
            
        }
    }
    
    public function setName($path_to_file, $path)
    {
        $this->session_id = $path_to_file;

        $this->read();
    }

    public function read()
    {
        $result = $this->db_connect->query('SELECT * FROM `sessions` WHERE session_id = ?', [$this->session_id]);
        
        if ($result->num_rows > 0){
            $this->data = json_decode($result->row['data'], true);
        } else {
            $this->db_connect->query('INSERT INTO `sessions` SET session_id = ?, ip = ?, `data` = "{}"', [$this->session_id, $_SERVER['REMOTE_ADDR']]);
            $this->data = [];
        }
    }

    /**
     * Получить значение из хранилища
     *
     * @param string $key
     * @return mixed
     */
    public function get(string $key)
    {
        $this->session_id = $key;
        $this->read();
        
        return $this->data;
    }

    /**
     * Установить значение в хранилище
     *
     * @param string $key
     * @param mixed $value
     * @param boolean $strict
     * @return void
     */
    public function set(string $key, $value,bool $strict = false)
    {
        $data = json_encode($value);
        $this->db_connect->query('UPDATE sessions SET `data` = ?, date_updated = NOW() WHERE session_id = ?', [$data, $this->session_id]);
    }

    /**
     * Проверить, существет ли данные в хранилище
     *
     * @param string $key
     * @return boolean
     */
    public function has(string $key)
    {
        return isset($this->data[$key]);
    }

    public function update()
    {
    }

    /**
     * Удалить значение по ключу
     *
     * @param string $key
     * @return void
     */
    public function unset(string $key)
    {
        if ($this->has($key)){
            unset($this->data[$key]);
        }
    }

    /**
     * Очистить хранилище
     *
     * @return void
     */
    public function clean()
    {
        $this->data = [];
    }

    /**
     * Получить группу значений или значение из группы
     *
     * @param string $groupKey
     * @param string $key
     * @return mixed
     */
    public function getGroup(string $groupKey, string $key = '')
    {
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

    function __destruct(){
        $this->update();
    }

}