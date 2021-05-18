<?php
  namespace framework\engine;

  class loader
  {
    protected $di = null;
    protected $currentInstance = null;
    protected $httpRequest = null;
    protected $routeParams = null;
    protected $currentFolder = null;

    public function __construct($di)
    {
      $this->di = $di;
    }

    public function setHttpRequest($httpRequest)
    {
      $this->httpRequest = $httpRequest;
    }

    public function setRouteParams($routeParams)
    {
      $this->routeParams = $routeParams;
    }

    /**
     * Грузить контроллер
     *
     * @return void
     */
    public function _controller($controllerName, $localStorage)
    {
      // Генерация пути до контроллера
      // Подключение контроллера
      // Возвращает экземпляр класса
      if(strpos($controllerName, '\\application') === false)
      $str = '\\application\\controllers\\' . $controllerName;
      else $str = $controllerName;

      if($this->currentFolder != null) {
        $str = $this->currentFolder . '\\' . $controllerName;
      }
      
      if(is_dir(__PATH__ . $str)) {
        $this->currentFolder = $str;
        return $this;
      }

      $this->currentFolder = null;
      $this->currentInstance = null;
      $controller = new $str($this);
      $controller->routeParams = $this->routeParams;
      $controller->httpRequest = $this->httpRequest;
      //$controller->routerParams = 
      if(!$localStorage->has('ctrl__' . $str)) $localStorage->set('ctrl__' . $str, $controller);

      return $localStorage->get('ctrl__' . $str);
    }
    /**
     * Грузить модель
     *
     * @return object
     */
    public function _model($modelName)
    {
      $str = '\\application\\models\\' . $modelName;

      if($this->currentFolder != null) {
        $str = $this->currentFolder . '\\' . $modelName;
      }
      
      if(is_dir(__PATH__ . $str)) {
        $this->currentFolder = $str;
        return $this;
      }

      $this->currentFolder = null;
      $name = 'mdl__' . str_replace('\\', '#', $str);
      
      if(!$this->di->get('localStorage')->has($name)) {
        $modelObject = new $str(null);
        $this->di->get('localStorage')->set($name, $modelObject);
      }
      return $this->di->get('localStorage')->get($name);
    }
    //Сделать поздним стат. связыванием
    //!ГОВНО
    public function __get($name)
    {
      if($name == 'di') return $this->di;
      if(in_array($name, ['model', 'controller']) || $this->currentInstance !== null) {
        if ($this->currentInstance !== null){
          $str = $this->currentInstance;
          return $this->$str($name, $this->di->get('localStorage'));
        }
        $this->currentInstance = '_' . $name;
        return $this;
      } 

      //$this->model->nominations->test()
      //proxy call

      $this->currentInstance = null;
      $instance = $this->di->get($name);
      return $instance;
    }
  }