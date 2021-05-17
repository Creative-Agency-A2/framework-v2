<?php
  namespace framework\engine;

  class loader
  {
    protected $di = null;
    protected $currentInstance = null;
    protected $httpRequest = null;
    protected $routeParams = null;

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

      $controller = new $str($this);
      $controller->routeParams = $this->routeParams;
      $controller->httpRequest = $this->httpRequest;
      //$controller->routerParams = 
      if(!$localStorage->has('ctrl__' . $controllerName)) $localStorage->set('ctrl__' . $controllerName, $controller);

      return $localStorage->get('ctrl__' . $controllerName);
    }
    /**
     * Грузить модель
     *
     * @return object
     */
    public function _model($modelName)
    {
      $str = '\\application\\models\\' . $modelName;
      if(!$this->di->get('localStorage')->has('mdl__' . $modelName)) $this->di->get('localStorage')->set('mdl__' . $modelName, new $str($this->di->get('db')));
      return $this->di->get('qq')->get('mdl__' . $modelName);
    }
    //Сделать поздним стат. связыванием
    //!ГОВНО
    public function __get($name)
    {
      if($name == 'di') return $this->di;
      if(in_array($name, ['model', 'controller']) || $this->currentInstance !== null) {
        if ($this->currentInstance !== null){
          $str = $this->currentInstance;
          $this->currentInstance = null;
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