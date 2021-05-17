<?php 
    namespace application\providers;

    class routerProvider extends \framework\engine\provider\provider
    {
        public $priority = 0;

        public function register($di)
        {
            $di->set('router', function ($obj, $params) {
                return new \framework\libraries\router\handler();
            });
        }

        public function boot($di)
        {
            $currentRequest = new \framework\libraries\http\httpFactoryRequest();
            $request = $currentRequest->createRequest(
                $_SERVER['REQUEST_METHOD'], 
                (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"
            );
            
            $request->setHeader(getallheaders());
            $request->setBody(file_get_contents('php://input'));
            $router = $di->get('router');

            if(file_exists(__DIR_APP__ . 'configs/routers.php'))
                require_once __DIR_APP__ . 'configs/routers.php';

            $data = $router->dispatch($request->getMethod(), $request->getUrl()->getPath());
            if($data == null) return 1;
            $loader = $di->get('loader');
            $loader->setHttpRequest($request);
            
            $loader->setRouteParams($data[2]);
            $methodName = $data[1];
            
            $controller = $loader->_controller($data[0], $di->get('localStorage'));
            $controller->$methodName();
        }
    }