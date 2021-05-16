<?php
/**
 * Родительский класс DI-контейнера
 * Запускается не данный контейнер, а его дочерний класс в приложении
 * Это сделано для того, чтобы можно было переопределить методы resolve, getDependencies и/или добавить новые методы
 */
namespace framework\engine\container;

class container extends \framework\engine\container\containerAbstract {

    /**
     * Массив классов, используемых в зависимостях
     *
     * @var array
     */
    protected $instances = [];

    /**
     * Массив экземпляров классов
     *
     * @var array
     */
    protected $objects = [];

    /**
     * Массив параметров класса
     *
     * @var array
     */
    protected $params = [];

    /**
     * Активный ключ
     *
     * @var string
     */
    protected $currentKey = '';

    /**
     * Массив явных зависимостей регистрируемых классов
     *
     * @var array
     */
    protected $dependencies = [];

    /**
     * Активная зависимость
     *
     * @var string
     */
    protected $currentDep;

    
    /**
     * Добавить новый класс в контейнер (название класса)
     *
     * @param string $key
     * @param boolean $value
     * @return void
     */
    public function set(string $key, $value = false)
    {
        if (!$value) $value = $key;
        $this->instances[$key] = $value;
        $this->currentKey = $key;
    }
    
    /**
     * Явно добавить зависимость к регистрируемому классу
     *
     * @param array $dependencies
     * @return void
     */
    public function dep(array $dependencies = [])
    {
        $this->dependencies[$this->currentKey] = $dependencies;
    }

    /**
     * Получить объект класса со всеми зависимостями
     * Все зависимые классы должны быть уже загружены
     *
     * @param string $key
     * @param array $params
     * @param boolean $singleton - этот параметр нужен для получения только одного экземпляра класса
     * @return void
     */
    public function get(string $key, array $params = [], bool $singleton = true): object
    {
        if (!$this->has($key)){
            $this->set($key);
        }

        if ($singleton && $this->hasObject($key)){
            return $this->objects[$key];
        }
      
        if ($singleton){
            $this->objects[$key] = $this->resolve($key, $params);
            return $this->objects[$key];
        }
    
        //return $this->resolve($key, $params);
    }
    
    /**
     * Проверка на существование класса в контейнере
     *
     * @param string $key
     * @return boolean
     */
    public function has(string $key) : bool 
    {
        return isset($this->instances[$key]);
    }

    /**
     * Проверка на существование экземпляра класса
     *
     * @param string $key
     * @return boolean
     */
    protected function hasObject(string $key) : bool 
    {
        return isset($this->objects[$key]);
    }

    /**
     * Получить экземпляр класса
     *
     * @param string $key
     * @return void
     */
    protected function getObject(string $key)
    {
        if ($this->hasObject($key)){
            return $this->objects[$key];
        }
        return null;
    }

    /**
     * Получить зависимости класса
     * Может быть переназначен в дочернем классе
     *
     * @param class $instance
     * @param array $params
     * @return class object
     */
    protected function resolve(string $key, array $params = [])
    {
        $instance = $this->instances[$key];
        if ($instance instanceof \Closure){
            return $instance($this, $params);
        }
    }

}