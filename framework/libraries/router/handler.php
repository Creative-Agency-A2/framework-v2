<?php
    namespace framework\libraries\router;

    class handler
    {
        private $routers = [];

        public function __construct()
        {
            
        }

        public function __call($name, $args) {
            if(in_array($name, ['get', 'post', 'put', 'delete'])) {
                $this->routers[] = ['method' => $name, 'path' => $args[0], 'handler' => $args[1], 'options' => $args[2]];
            }
        }

        public function prepare()
        {
            for($i = 0, $quantity = count($this->routers); $i < $quantity; $i++) {
                $match = [];
                preg_match('/{[a-zA-Z_]+}/', $this->routers[$i]['path'], $match);
                $m = [];
                preg_match_all('/{([a-zA-Z_]+)}/', $this->routers[$i]['path'], $m);
                $this->routers[$i]['path'] = str_replace('/', '#', preg_replace('/{[a-zA-Z_]+}/', '([a-zA-Z\d_]+)', $this->routers[$i]['path']));
                
                for($j = 0; $j < count($match); $j++) {
                    $match[$j] = str_replace(['{', '}'], '', $match[$j]);
                }
                if(count($m) > 0)
                array_splice($m, 0, 1);
                $this->routers[$i]['name_params'] = $m[0];
                $this->routers[$i]['params'] = $match;
            }
        }

        public function dispatch($method, $url)
        {
            $url = str_replace('/', '#', $url);
            $this->prepare();
            for($i = 0, $quantity = count($this->routers); $i < $quantity; $i++) {
                if($this->routers[$i]['method'] == strtolower($method)) {
                    $match = [];
                    if(preg_match('/^' . $this->routers[$i]['path'] . '$/', $url, $match)) {
                        
                        if(count($match) > 0)
                        array_splice($match, 0, 1);

                        $return = array_merge(explode('::', $this->routers[$i]['handler']), [array_combine($this->routers[$i]['name_params'], $match)]);
                        return $return;
                    }
                }
            }
        }
    }