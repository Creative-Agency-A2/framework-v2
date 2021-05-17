<?php

namespace framework\libraries\http\interfaces;

use framework\libraries\http\interfaces\MessageInterface;
use framework\libraries\http\interfaces\UriInterface;

interface RequestInterface extends MessageInterface {

    /**
     * Получить URL, куда будет отправлен этот запрос
     *
     * @return string
     */
    public function getRequestTarget() : string;

    /**
     * Получить экземпляр с указанным URL для отправления запроса
     *
     * @param string $requestTarget
     * @return static
     */
    public function withRequestTarget(string $requestTarget);

    /**
     * Получить метод запроса
     *
     * @return string
     */
    public function getMethod() : string;

    /**
     * Получить экземпляр запроса с указанным методом
     *
     * @param string $method
     * @return static
     * @throws \InvalidArgumentException
     */
    public function withMethod(string $method);

    /**
     * Получить URI данного запроса
     *
     * @return UriInterface
     */
    public function getUri();

    /**
     * Получить экземпляр запроса с указанным URI
     *
     * @param UriInterface $uri
     * @param bool $preserveHost Сохранить исходное состояние заголовка host
     * @return void
     */
    public function withUri(UriInterface $uri, bool $preserveHost);

}