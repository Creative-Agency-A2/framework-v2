<?php
/**
 * Класс работы с cURL
 */
namespace framework\libraries\http;

class curl {

    /**
     * Используемый метод HTTP-запроса
     *
     * @var string
     */
    private $method = 'get';

    /**
     * URL, куда будет отправлен запрос
     *
     * @var string
     */
    private $url = '';

    /**
     * Массив заголовков
     *
     * @var array
     */
    private $headers = [];

    private $response_headers = [];

    /**
     * Содержимое запроса
     *
     * @var array
     */
    private $body = [];

    /**
     * Опции запроса
     * 
     * [
     *   curl - опции, задаваемые через curl_setopt
     *   filters - массив коллбэков, которые по порядку будут менять значение ответа cURL
     *   middlewares - массив коллбэков, которые по порядку выполняются перед curl_setopt_array и могут поменять запрос
     * ]
     *
     * @var array
     */
    private $options = [];

    /**
     * Активное соединение
     * curl_init()
     *
     * @var return curl_init()
     */
    private $connect;

    /**
     * Опции запроса cURL
     *
     * @var array
     */
    private $curl_options = [];

    /**
     * Счетчик запросов
     *
     * @var integer
     */
    private $requestCount = 0;

    /**
     * Отправка запроса
     *
     * @param string $method
     * @param string $url
     * @param array $headers
     * @param array $body
     * @param array $options
     * @return array
     */
    public function send($method, $url, $headers, $body, $options){
        
        $this->method = $method;
        $this->url = $url;
        $this->headers = $headers;
        $this->body = $body;
        $this->options = $options;

        if (isset($this->options['curl'])){
            $this->curl_options = $this->options['curl'];
        }

        $this->connect = curl_init();
        $this->middlewares();
        $this->setOptions();

        return $this->call();
    }

    /**
     * Устанавливаем опции запроса
     *
     * @return void
     */
    private function setOptions()
    {
        if ( $this->isPost() ){
            $this->curl_options[CURLOPT_POST] = 1;
        } else {
            $this->curl_options[CURLOPT_POST] = 0;
        }
        
        $this->curl_options[CURLOPT_HTTPHEADER] = $this->getHeaders();

        if (!isset($this->curl_options[CURLOPT_RETURNTRANSFER])){
            $this->curl_options[CURLOPT_RETURNTRANSFER] = 1;
        }

        $this->curl_options[CURLOPT_HEADERFUNCTION] = [$this, 'headerCallback'];

        /* if (!isset($this->curl_options[CURLINFO_HEADER_OUT])){
            $this->curl_options[CURLINFO_HEADER_OUT] = 0;
        } */

        if (!isset($this->curl_options[CURLOPT_HEADER])){
            $this->curl_options[CURLOPT_HEADER] = 0;
        }


        if (!isset($this->curl_options[CURLOPT_SSL_VERIFYPEER])){
            $this->curl_options[CURLOPT_SSL_VERIFYPEER] = 0;
        }

        if (!isset($this->curl_options[CURLOPT_NOBODY])){
            $this->curl_options[CURLOPT_NOBODY] = 0;
        }

        $this->curl_options[CURLOPT_URL] = $this->url . $this->buildQuery();
        $this->curl_options[CURLOPT_POSTFIELDS] = $this->buildPostFields();
    }

    /**
     * Функция обратного вызова для получения заголовков из cURL запроса
     *
     * @param curl_object $ch
     * @param string $header_line
     * @return int
     */
    private function headerCallback($ch, $header_line)
    {
        $this->response_headers[] = $header_line;
        return strlen($header_line);
    }

    /**
     * Получаем заголовки, устанавливаем значения по умолчанию и форматируем перед отправкой
     *
     * @return array
     */
    private function getHeaders(){
        $headers = $this->headers;
        $headers['Content-Type'] = $headers['Content-Type'] ?? 'application/json';
        $headers['User-Agent'] = $headers['User-Agent'] ?? 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88 Safari/537.36';
        $headers['Referer'] = $headers['Referer'] ?? 'localhost';

        $return_headers = [];
        foreach ($headers as $key => $header) {
            $return_headers[] = $key . ': ' . $header; 
        }
        return $return_headers;
    }

    /**
     * Парсим заголовки ответа
     *
     * @return array
     */
    private function formatHeaders() : array
    {
        $response = [];
        if (!empty($this->response_headers)){
            foreach ($this->response_headers as $key => $value) {
                if ($key !== 0){
                    $header = explode(':', $value, 2);
                    $header_key = $header[0];
                    $header_value = $header[1] ?? false;
                    
                    if ($header_value){
                        if ( isset($response[$header_key]) ){
                            if ( is_array($response[$header_key]) ){
                                $response[$header_key][] = trim($header_value);
                            } else {
                                $temp_header_value = $response[$header_key];
                                $response[$header_key] = [];
                                $response[$header_key][] = $temp_header_value;
                                $response[$header_key][] = trim($header_value);
                            }
                        } else {
                            $response[$header_key] = trim($header_value);
                        }
                    }
                }
            }
        }
        return $response;
    }

    /**
     * Выполнение запроса
     *
     * @return void
     */
    private function call()
    {
        $this->requestCount++;
        \curl_setopt_array($this->connect, $this->curl_options);
        $response = curl_exec($this->connect);
        $return_array = [
            'response' => $response,
            'headers'  => $this->formatHeaders(),
            'info'  => \curl_getinfo($this->connect),
            'error' => [
                'number' => \curl_errno($this->connect),
                'text'   => \curl_error($this->connect)
            ]
        ];
        $return_array = $this->runFilters( $return_array );
        \curl_close($this->connect);
        $this->requestCount = 0;
        return $return_array;
    }

    public function middlewares()
    {
        if (!isset($this->options['middlewares'])) return false;

        foreach ($this->options['middlewares'] as $middleware){
            $middleware($this);
        }
    }

    /**
     * Запускаем фильтры, при их наличии
     *
     * @param array $value
     * @return array
     */
    public function runFilters($value)
    {
        if (!isset($this->options['filters'])) return $value;
        foreach ($this->options['filters'] as $filter){
            $result = $filter($value, $this);
            if (!isset($result['info'])){
                $result['info'] = $value['info'];
            }
            if (!isset($result['error'])){
                $result['error'] = $value['error'];
            }
        }
        return $result;
    }

    /**
     * Собираем параметры запроса, в зависимости от метода запроса
     *
     * @return string
     */
    public function buildQuery()
    {
        if ( $this->isGet() ){
            return '?' . http_build_query($this->body);
        }
        return '';
    }

    /**
     * Собираем параметры для POST Запроса, в зависимости от метода запроса
     *
     * @return string|array
     */
    public function buildPostFields()
    {
        if ( $this->isPost() ){
            return http_build_query($this->body);
        }
        return [];
    }

    /**
     * Является ли запрос GET-запросом
     *
     * @return boolean
     */
    public function isGet() : bool
    {
        return \strtoupper($this->method) == 'GET';
    }

    /**
     * Является ли запрос POST-запросом
     *
     * @return boolean
     */
    public function isPost() : bool
    {
        return \strtoupper($this->method) == 'POST';
    }

}