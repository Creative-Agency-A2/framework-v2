<?php
    namespace application\controllers;

    class home extends \framework\engine\controller
    {
        public function index()
        {
            $this->controller->admin->test->home->index();
        }
    }