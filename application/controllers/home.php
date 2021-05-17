<?php
    namespace application\controllers;

    class home extends \framework\engine\controller
    {
        public function index()
        {
            var_dump($this->httpRequest, $this->routeParams);
        }
    }