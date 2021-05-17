<?php
    namespace framework\engine;

    class controller
    {
        public $routeParams = [];
        public $httpRequest = [];

        protected $loader = null;

        function __construct($loader)
        {
          $this->loader = $loader;
        }
    
        function __get($name)
        {
          return $this->loader->$name;
        }
    }