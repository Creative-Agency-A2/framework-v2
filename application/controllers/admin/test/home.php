<?php
    namespace application\controllers\admin\test;

    class home extends \framework\engine\controller
    {
        public function index()
        {
            $this->model->admin->test->home->test();
        }
    }