<?php

namespace framework\libraries\http\interfaces;

use framework\libraries\http\interfaces\RequestInterface;
use framework\libraries\http\interfaces\ResponseInterface;

interface ClientInterface {

    public function sendRequest(RequestInterface $request) : ResponseInterface;

}