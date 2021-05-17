<?php

namespace framework\libraries\http;

class httpFactoryResponse {

    /**
     * Undocumented function
     *
     * @param string $content
     * @return \framework\libraries\http\httpResponse
     */
    public function createResponse(string $content = null) : \framework\libraries\http\response
    {
        return new response($content);
    }
}