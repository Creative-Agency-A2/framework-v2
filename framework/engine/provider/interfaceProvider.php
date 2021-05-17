<?php
  namespace framework\engine\provider;

  interface interfaceProvider
  {
    public function register($di);
    public function boot($di);
  }