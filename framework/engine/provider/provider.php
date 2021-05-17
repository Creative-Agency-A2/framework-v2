<?php
  namespace framework\engine\provider;

  class provider implements \framework\engine\provider\interfaceProvider
  {
    protected $config = [];

    public $priority = 1;

    public function __construct()
    {
      return $this;
    }

    public function register($di)
    {

    }

    public function boot($di)
    {
      
    }
  }