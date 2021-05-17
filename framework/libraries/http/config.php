<?php

namespace framework\libraries\http;

class config {

    /**
     * @var array
     */
    protected $data = [
        'headers' => [
            'Content-type' => 'application/json',
            'User-Agent' => 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88 Safari/537.36',
            'Referer' => __MAIN_URL__ 
        ]
    ];

    /**
     * Undocumented function
     *
     * @param string $key
     * @param mixed $value
     * @param string $groupKey
     * @return void
     */
    protected function set(string $key, $value, string $groupKey = '')
    {
        if ('' == $groupKey){
            $this->data[$key] = $value;
        } else {
            $this->data[$groupKey][$key] = $value;
        }
    }

    /**
     * Undocumented function
     *
     * @param string $key
     * @param string $groupKey
     * @return mixed
     */
    protected function get(string $key, string $groupKey = '') 
    {
        if ('' !== $groupKey){
            return isset($this->data[$groupKey][$key]) ? $this->data[$groupKey][$key] : false;
        } else {
            return isset($this->data[$key]) ? $this->data[$key] : false;
        }
    }
}