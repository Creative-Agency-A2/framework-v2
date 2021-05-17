<?php
/**
 * Библиотека для работы с HTTP-запросами
 */
namespace framework\libraries\http;

class http {

    /**
     * Объект для работы с запросом
     *
     * @var request
     */
    public $request;

    /**
     * Объект для обработки ответа
     *
     * @var response
     */
    public $response;

    public function __construct($request = null, $response = null)
    {
        $this->setRequest($request);
        $this->setResponse($response);
    }

    /**
     * Устанавливаем объект для работы с запросами
     *
     * @param null|request $request
     * @return void
     */
    public function setRequest($request = null)
    {
        if (is_null($request)) $request = $this->createRequest( new \framework\libraries\http\curl()  );
        $this->request = $request;
    }

    /**
     * Устанавливаем объект для обработки ответа
     *
     * @param null|httpResponse $response
     * @return void
     */
    public function setResponse($response = null)
    {
        if (is_null($response)) $response = $this->createResponse();
        $this->response = $response;
    }

    /**
     * Создаем экземпляр базового объекта для работы с запросом
     *
     * @param curl $client
     * @return request
     */
    public function createRequest($client)
    {
        return new request($client);
    }

    /**
     * Создаем экземпляр базового объекта для обработки ответа
     *
     * @return response
     */
    public function createResponse()
    {
        return new response();
    }

    public function request(string $method = 'get', string $url = '', array $headers = [], array $body = [], array $options = [])
    {
        //httpFactoryRequest
        //
        $this->setRequest(null);
        $this->setResponse(null);
        
        $this->request->setMethod($method);
        $this->request->setUrl($url);
        $this->request->setHeader($headers);
        $this->request->setBody($body);
        $this->request->setOptions($options);

        $response = $this->request->send();
        $this->response->setResponse($response);
        return $this->response;
    }

    /**
     * Отправляем запрос
     * Если указан request, значит, возможно, запрос повторный, просто с другими параметрами
     * Для обработки ответа необходимо создать новый экземпляр класса response
     * 
     * @param null|request $request
     * @return httpResponse
     */
    public function send($request = null){
        if (is_null($request)) {
            $request = $this->request;
            $response = $this->response;
        } else {
            $response = $this->createResponse();
        }
        $output = $request->send();
        $response->setRequestParams($request->getParams());
        $response->setResponse($output);
        return $this->response;
    }
}