<?php

namespace framework\libraries\http;

class request {

    /**
     * Активное соедние cURL
     *
     * @var \framework\libraries\http\curl
     */
    protected $connect;

    /**
     * Метод запроса
     *
     * @var string
     */
    protected $method = 'get';

    /**
     * URL, куда необходимо сделать запрос
     *
     * @var string
     */
    protected $url = '';

    /**
     * Массив заголовков
     *
     * @var array
     */
    protected $headers = [];

    /**
     * Массив тела запроса
     *
     * @var array
     */
    protected $body = [];

    /**
     * Опции запроса
     *
     * @var array
     */
    protected $options = [];

    public function __construct($connect = null){
        $this->connect = $connect;
    }

    /**
     * Указать метод запроса
     *
     * @param string $method
     * @return self
     */
    public function setMethod(string $method = 'get') {
        $this->method = $method;
        return $this;
    }

    /**
     * Установить заголовки
     * Можно отправить массив setHeader(['Content-type' => 'application/json']); 
     * В этом случае заголовки будут перезаписны полностью
     * 
     * Также, можно указать 2-мя аргументами setHeader('Content-type', 'application/json');
     * В этом случае отдельный заголовок будет переписан, а остальные останутся такими же
     * 
     * @param array|string $header_data
     * @param string $value
     * @return self
     */
    public function setHeader($header_data, $value = ''){
        if (is_array($header_data)){
            $this->headers = $header_data;
        } else {
            $this->headers[$header_data] = $value;
        }
        return $this;
    }

    /**
     * Установить URL запроса
     *
     * @param string $url
     * @return self
     */
    public function setUrl(string $url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * Указать тело запроса
     *
     * @param array $body
     * @return self
     */
    public function setBody($body = null){
        $this->body = $body;
        return $this;
    }

    /**
     * Установить обработчик для ответа cURL
     *
     * @param \Closure $filter
     * @return self
     */
    public function setFilter(\Closure $filter)
    {
        $this->options['filters'][] = $filter;
        return $this;
    }

    /**
     * Установить опции запроса
     *
     * @param array $options
     * @return self
     */
    public function setOptions(array $options = []){
        $this->options = [];
        return $this;
    }

    /**
     * Отправить запрос
     *
     * @return \framework\libraries\http\curl->send();
     */
    public function send()
    {
        /////
        return $this->connect->send($this->method, $this->url, $this->headers, $this->body, $this->options);
    }

    /**
     * Получить все параметры запроса
     *
     * @return array
     */
    public function getParams()
    {
        return [
            'method' => $this->method,
            'url'    => $this->url,
            'headers'=> $this->headers,
            'body'   => $this->body,
            'options' => $this->options
        ];
    }

    public function withBody(array $body) : self
    {
        $new = clone $this;
        $new->setBody($body);
        return $new;
    }

    public function getMethod()
    {
        return strtolower($this->method);
    }

    public function getUrl()
    {
        return new uri($this->url);
    }

}