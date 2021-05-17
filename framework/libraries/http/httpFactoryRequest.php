<?php

namespace framework\libraries\http;

class httpFactoryRequest {

    /**
     * Undocumented function
     *
     * @param string $method
     * @param string $uri
     * @return \framework\libraries\http\httpRequest
     */
    public function createRequest(string $method, string $uri ) : \framework\libraries\http\request
    {
        $request = new request();
        $request->setUrl($uri);
        $request->setMethod($method);

        return $request;
    }
}