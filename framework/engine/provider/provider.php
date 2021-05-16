<?php
  namespace framework\engine\provider;

  class provider implements \framework\engine\provider\ProviderInterface
  {
    protected $config = [];

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