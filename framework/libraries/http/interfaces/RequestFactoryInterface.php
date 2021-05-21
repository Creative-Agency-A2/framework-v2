<?php

namespace framework\libraries\http\interfaces;

use framework\libraries\http\interfaces\RequestInterface;

interface RequestFactoryInterface {
    
    public function createRequest(
        string $uri, 
        string $method, 
        $body, 
        array $headers
    ) : RequestInterface;
}