<?php

namespace framework\libraries\http;

use framework\libraries\http\interfaces\RequestInterface;
use framework\libraries\http\interfaces\StreamInterface;
use framework\libraries\http\interfaces\UriInterface;

class Request implements RequestInterface {

    /**
     * Поддерживаемые методы
     *
     * @var array
     */
    private $validMethods = [
        'CONNECT',
        'DELETE',
        'GET',
        'HEAD',
        'OPTIONS',
        'PATCH',
        'POST',
        'PUT',
        'TRACE',
    ];

    /**
     * @var string
     */
    private $method;

    /**
     * @var UriInterace
     */
    private $uri;

    /**
     * @var StreamInterface
     */
    private $stream;

    /**
     * @var array
     */
    private $headers = [];

    /**
     * @var array
     */
    private $headerNames = [];

    /**
     * @var null|string
     */
    private $requestTarget;

    /**
     * @var string
     */
    private $protocol = '1.1';

    public function __construct(string $uri = '', string $method = '', $body = 'php://memory', array $headers = []){
        $this->initRequest($uri, $method, $body, $headers);
    }

    /**
     * Получить URL, куда будет отправлен этот запрос
     *
     * @return string
     */
    public function getRequestTarget() : string
    {
        if (null !== $this->requestTarget){
            return $this->requestTarget;
        }

        if (!$this->uri){
            return '/';
        }

        $target = $this->uri->getPath();
        if ($this->uri->getQuery()){
            $target .= '?' . $this->uri->getQuery();
        }

        if (empty($target)){
            $target = '/';
        }

        return $target;
    }

    /**
     * Получить экземпляр с указанным URL для отправления запроса
     *
     * @param string $requestTarget
     * @return static
     */
    public function withRequestTarget(string $requestTarget)
    {
        if (preg_match('#\s#', $requestTarget)) {
            throw new InvalidArgumentException(
                'Некоррекный URI запроса; нельзя использовать пробельные символы'
            );
        }

        $new = clone $this;
        $new->requestTarget = $requestTarget;
        return $new;
    }

    /**
     * Получить метод запроса
     *
     * @return string
     */
    public function getMethod() : string
    {
        return $this->method;
    }

    /**
     * Получить экземпляр запроса с указанным методом
     *
     * @param string $method
     * @return static
     * @throws \InvalidArgumentException
     */
    public function withMethod(string $method)
    {
        $this->validateMethod($method);
        $new = clone $this;
        $new->method = $method;
        return $new;
    }

    /**
     * Получить URI данного запроса
     *
     * @return UriInterface
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * Получить экземпляр запроса с указанным URI
     *
     * @param UriInterface $uri
     * @param bool $preserveHost Сохранить исходное состояние заголовка host
     * @return void
     */
    public function withUri(UriInterface $uri, bool $preserveHost)
    {
        $new = clone $this;
        $new->uri = $uri;

        if ($preserveHost) {
            return $new;
        }

        if (! $uri->getHost()) {
            return $new;
        }

        $host = $uri->getHost();
        if ($uri->getPort()) {
            $host .= ':' . $uri->getPort();
        }

        $new->headerNames['host'] = 'Host';
        $new->headers['Host'] = array($host);

        return $new;
    }

        /**
     * Возвращает версию HTTP-протокола (например: 1.0 или 1.1)
     *
     * @return string
     */
    public function getProtocolVersion() : string
    {
        return $this->protocol;
    }

    /**
     * Вернуть экземпляр с указанной версией протокола
     *  
     * @param string $version
     * @return static
     */
    public function withProtocolVersion(string $version)
    {
        $new = clone $this;
        $new->protocol = $version;
        return $new;
    }

    /**
     * Вернуть двумерный массив из заголовков
     * 
     *
     * @return array
     */
    public function getHeaders()
    {
        $headers = $this->headers;
        if (! $this->hasHeader('host')
            && ($this->uri && $this->uri->getHost())
        ) {
            $headers['Host'] = [$this->getHostFromUri()];
        }

        return $headers;
    }

    /**
     * Проверить существование заголовка по имени
     *
     * @param string $name
     * @return boolean
     */
    public function hasHeader(string $name) : bool
    {
        return array_key_exists(strtolower($header), $this->headerNames);
    }

    /**
     * Получить заголовок по имени в формате массива
     * Например, для заголовка:
     * Accept : text/html, application/json
     * 
     * При запросе message->getHeader('Accept') вернется:
     * [
     *  'text/html',
     *  'application/json'
     * ]
     * 
     * @param string $name
     * @return string[]
     */
    public function getHeader(string $name)
    {
        if (! $this->hasHeader($header)) {
            if (strtolower($header) === 'host'
                && ($this->uri && $this->uri->getHost())
            ) {
                return [$this->getHostFromUri()];
            }

            return [];
        }

        $header = $this->headerNames[strtolower($header)];

        $value = $this->headers[$header];
        $value = is_array($value) ? $value : [$value];
        return $value;
    }

    /**
     * Получить заголовок по имени в строковом формате
     * Например, для заголовка:
     * Accept : text/html, application/json
     * 
     * При запросе message->getHeaderLine('Accept') вернется:
     * text/html, application/json
     * 
     * @param string $name
     * @return string
     */
    public function getHeaderLine(string $name) : string
    {
        $value = $this->getHeader($header);

        if (empty($value)) {
            return null;
        }

        return implode(',', $value);
    }

    /**
     * Вернуть экземпляр с указанным заголовком
     * Перезаписывает заголовок
     *
     * @param string $name
     * @param string|string[] $value
     * @return static
     * @throws \InvalidArgumentException
     */
    public function withHeader(string $name, $value)
    {
        if (is_string($value)) {
            $value = [ $value ];
        }

        if (! is_array($value) || ! $this->arrayContainsOnlyStrings($value)) {
            throw new \InvalidArgumentException(
                'Некорректное значение заголовка; Заголовок должен быть строкой или массивом строк'
            );
        }

        $this->assertValidHeaderName($name);
        $this->assetValidHeader($value);

        $normalized = strtolower($name);

        $new = clone $this;

        $new->headerNames[$normalized] = $name;
        $new->headers[$name] = $value;
        return $new;
    }


    /**
     * Вернуть экземпляр с дополненным заголовком
     * Не перезаписывает заголовок, а дополняет его, например:
     * При вызове message->withHeader('accept', 'text/html')->withHeader('accept', 'application/json')
     * Результатом будет экзепляр класса с заголовком accept : text/html, application/json
     * 
     * @param string $name
     * @param string|string[] $value
     * @return static
     * @throws \InvalidArgumentException
     */
    public function withAddedHeader(string $name, $value)
    {
        if (is_string($value)) {
            $value = [ $value ];
        }

        if (! is_array($value) || ! $this->arrayContainsOnlyStrings($value)) {
            throw new \InvalidArgumentException(
                'Некорректное значение заголовка; Заголовок должен быть строкой или массивом строк'
            );
        }

        $this->assertValidHeaderName($name);
        $this->assetValidHeader($value);

        $normalized = strtolower($name);

        $new = clone $this;
        $new->headers[$header] = array_merge($this->headers[$header], $value);
        return $new;
    }

    /**
     * Вернуть экземпляр без указанного заголовка
     *
     * @param string $name
     * @return static
     */
    public function withoutHeader(string $name)
    {
        if (! $this->hasHeader($name)) {
            return clone $this;
        }

        $normalized = strtolower($name);
        $original   = $this->headerNames[$normalized];

        $new = clone $this;
        unset($new->headers[$original], $new->headerNames[$normalized]);
        return $new;
    }

    /**
     * Получить тело сообщения
     *
     * @return StreamInterface
     */
    public function getBody() : StreamInterface
    {
        return $this->stream;
    }

    /**
     * Вернуть экземпляр с указанным телом сообщения
     *
     * @param StreamInterface $body
     * @return static
     * @throws \InvalidArgumentException
     */
    public function withBody(StreamInterface $body)
    {
        $new = clone $this;
        $new->stream = $body;
        return $new;
    }

    /**
     * Инициализация запроса
     *
     * @param string $uri
     * @param string $method
     * @param string|resource|StreamInterface $body
     * @param array $header
     * @return void
     */
    private function initRequest($uri, $method, $body, $header)
    {
        if (!is_string($uri)){
            throw new \InvalidArgumentException("Параметр URI должен быть строкой");
        }

        $this->validateMethod($method);

        if (!is_string($body) && !$body instanceof StreamInterface && !\is_resource($body)){
            throw new \InvalidArgumentException("Параметр body должен быть либо строкой, либо соответствовать интерфейсу StreamInterface, либо быть ресурсом");
        }

        $this->uri = new Uri($uri);

        $this->stream = ($body instanceof StreamInterface) ? $body : new Stream($body, 'r');
        
        list($headers, $this->headerNames) = $this->filterHeaders($headers);
        $this->assertHeaders($headers);
        $this->headers = $headers;
    }

    /**
     * Отфильтровать заголовки
     *
     * @param array $originalHeaders
     * @return array [headers, headerNames]
     */
    private function filterHeaders(array $originalHeaders)
    {
        $headerNames = [];
        $headers = [];

        foreach ($originalHeaders as $header => $value) {
            if (!is_string($header)){
                continue;
            }
            if (!is_string($value) && !is_array($value)){
                continue;
            }
            if (!is_array($value)){
                $value = [$value];
            }
            $headerNames[strtolower($header)] = $header;
            $headers[$header] = $value;
        }
        return [$headers, $headerNames];
    }

    /**
     * Проверка правильности подаваемого метода запроса
     *
     * @param string $method
     * @throws \InvalidArgumentException
     * @return void
     */
    private function validateMethod($method)
    {
        if ($method === ''){
            return true;
        }

        if ( !is_string($method) ){
            throw new \InvalidArgumentException(sprintf(
                'Неподдерживаемый HTTP-метод; подаваемый параметр должен быть строкой, а не %s',
                (is_object($method) ? get_class($method) : gettype($method))
            ));
        }

        if ( !in_array(strtoupper($method), $this->validMethods, true) ) {
            throw new InvalidArgumentException(sprintf(
                'Метод "%s" не поддерживается',
                $method
            ));
        }

    }

    /**
     * Проверить заголовки
     *
     * @param array $headers
     * @return void
     */
    private function assertHeaders(array $headers)
    {
        foreach ($headers as $name => $headerValues) {
            $this->assertValidHeaderName($name);

            if (is_array($headerValues)){
                foreach ($headerValues as $headerValueKey => $value) {
                    $this->assetValidHeader($value);
                }
            }
        }
    }

    /**
     * Проверить название заголовка
     *
     * @param string $name
     * @throws \InvalidArgumentException
     * @return void
     */
    private function assertValidHeaderName(string $name)
    {
        if (! preg_match('/^[a-zA-Z0-9\'`#$%&*+.^_|~!-]+$/', $name)) {
            throw new \InvalidArgumentException('Некорректное название заголовка');
        }
    }

    /**
     * Проверить значение заголовка
     *
     * @param string $value
     * @return void
     */
    private function assetValidHeader(string $value)
    {
        if (!$this->headerIsValid($value)){
            throw new \InvalidArgumentException('Некорректное значение заголовка');
        }
    }

    /**
     * Проверка значения заголовка на допустимый формат и значения
     *
     * @param string $value
     * @return boolean
     */
    private function headerIsValid(string $value) : bool
    {   
        /**
         * Для переносов строк:
         * \n не должен быть без \r
         * \r не должен быть без \n
         * \r\n не должен быть пробела или горзонтальной табуляции
         * (Это все CRLF атаки)
         */
        if (preg_match("#(?:(?:(?<!\r)\n)|(?:\r(?!\n))|(?:\r\n(?![ \t])))#", $value)) {
            return false;
        }

        $length = strlen($value);
        for ($i = 0; $i < $length; $i += 1) {
            $ascii = ord($value[$i]);

            /**
             * Невидимые и непробельные символы
             * 9 - горизонтальная табуляция
             * 10 - перевод строки
             * 13 - возврат каретки
             * 32-126, 128-254 - видимые символы
             * 127 - DEL
             * 255 - пустой байт
             */
            if (($ascii < 32 && ! in_array($ascii, [9, 10, 13], true))
                || $ascii === 127
                || $ascii > 254
            ) {
                return false;
            }
        }

        return true;
    }

    /**
     * Проверить, чтобы все значения массива были строками
     *
     * @param array $array
     * @return boolean
     */
    private function arrayContainsOnlyStrings(array $array) : bool
    {
        foreach ($array as $key => $value) {
            if (!is_string($value)) return false;
        }
        return true;
    }

    /**
     * Получить значение host из URI
     *
     * @return string
     */
    private function getHostFromUri() : string
    {
        $host  = $this->uri->getHost();
        $host .= $this->uri->getPort() ? ':' . $this->uri->getPort() : '';
        return $host;
    }
}