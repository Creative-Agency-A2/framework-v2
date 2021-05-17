<?php
/**
 * Класс для работы с HTTP-ответом
 */
namespace framework\libraries\http;

class response {

    /**
     * Массив фраз по статусам
     *
     * @var array
     */
    private $phrases = [
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-status',
        208 => 'Already Reported',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => 'Switch Proxy',
        307 => 'Temporary Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Time-out',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Large',
        415 => 'Unsupported Media Type',
        416 => 'Requested range not satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        425 => 'Unordered Collection',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        451 => 'Unavailable For Legal Reasons',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Time-out',
        505 => 'HTTP Version not supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',
        511 => 'Network Authentication Required',
    ];

    /**
     * Результат работы cURL
     * [
     *     'response' - тело ответа
     *     'info' - результат curl_getinfo()
     *     'error' => [
     *          'number' - результат работы curl_errno
     *          'text'   - результат работы curl_error
     *     ]
     * ]
     *
     * @var array
     */
    public $httpRequestResponse;

    /**
     * Массив параметров запроса
     * [method, url, headers, body, options]
     * @var array
     */
    protected $requestParams = [];

    /**
     * Массив параметров ответа
     *
     * @var array
     */
    public $data = [];

    public function __construct(string $content = null) {
        if($content != null)
        $this->httpRequestResponse['response'] = $content;
    }

    /**
     * Установить ответ для парсинга
     *
     * @param array $httpRequestResponse
     * @return void
     */
    public function setResponse(array $httpRequestResponse = []){
        $this->httpRequestResponse = $httpRequestResponse;
        $this->parseResponse();
        return $this;
    }

    /**
     * Парсим данные ответа
     *
     * @return void
     */
    public function parseResponse()
    {
        $this->data = $this->httpRequestResponse['info'];
        $this->data['full_code'] = $this->getStatus() . ' ' . $this->getReasonPhrase(); 
        $this->headers = $this->outputExplode($this->httpRequestResponse['response']);
      //  var_dump($this->headers);
    }

    private function outputExplode($output)
    {
        $headers = [];
        $output = rtrim($output);
        $data = explode("\n", $output);
        //print_r($data);
        $headers['status'] = $data[0];
        array_shift($data);

        foreach($data as $part){
            $middle = explode(":", $part, 2);
            if ( !isset($middle[1]) ) { 
                $middle[1] = null; 
            }

            $headers[trim($middle[0])] = trim($middle[1]);
        }
        return $headers;
    }

    /**
     * Устанавливаем параметры запроса
     *
     * @param array $requestParams
     * @return void
     */
    public function setRequestParams(array $requestParams = [])
    {
        $this->requestParams = $requestParams;
    }

    /**
     * Получить статус ответа
     *
     * @return int
     */
    public function getStatus() : int
    {
        return (int) $this->data['http_code'];
    }

    /**
     * Получить ответ в формате 200 OK
     *
     * @return string
     */
    public function getReasonPhrase() : string
    {
        return (string) $this->phrases[ $this->data['http_code'] ];
    }

    public function getHeaders(){

    }

    /**
     * Получить Content-Type ответа
     *
     * @return string
     */
    public function getContentType() : string
    {
        return (string) $this->data['content_type'];
    }

    /**
     * Получить содержимое ответа
     * Если возвращаемый Content-Type = application/json - возвращаем сразу декодированный массив
     *
     * @return array|string
     */
    public function getBody(){
        if ($this->getContentType() == 'application/json'){
            return json_decode($this->httpRequestResponse['response'], true);
        }
        return $this->httpRequestResponse['response'];
    }
}