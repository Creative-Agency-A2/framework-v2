<?php
  namespace framework\engine\provider;

  interface ProviderInterface
  {
    public function register($di);
    public function boot($di);
  }