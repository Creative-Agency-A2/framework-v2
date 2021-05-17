<?php
    namespace framework\engine;

    class providers
    {
        public function __construct($di)
        {
            $dir = scandir(__DIR_PROVIDERS__);
            $dir = array_splice($dir, 2);
            $providers = [];

            for ($i = 0, $quantity = count($dir); $i < $quantity; $i++) {
                $name = '\\application\providers\\' . explode('.', $dir[$i])[0];
                $entity = new $name();
                $entity->register($di);

                $providers[] = ['object' => $entity, 'priority' => $entity->priority];
            }
            
            usort($providers, function ($a, $b) {
                return $b['priority'] - $a['priority'];
            });
            
            for($i = 0, $quantity = count($providers); $i < $quantity; $i++) {
                $providers[$i]['object']->boot($di);
            }
        }
    }