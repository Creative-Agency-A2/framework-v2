<?php 
    namespace application\providers;

    class httpProvider extends \framework\engine\provider\provider
    {
        public function register($di)
        {
            $di->set('http', function(&$obj, $params){
                return new \framework\libraries\http\http();
            });
        }
        public function boot($di)
        {
        }
    }