<?php

namespace framework\libraries\http\interfaces;

use framework\libraries\http\interfaces\MessageInterface;

interface ServerRequestInterface extends MessageInterface {

    /**
     * Получить параметры из $_SERVER
     *
     * @return array
     */
    public function getServerParams() : array;

    /**
     * Получить параметры из $_COOKIE
     *
     * @return array
     */
    public function getCookieParams() : array;

    /**
     * Получить клон запроса с указанными куками
     *
     * @param array $cookie [key,value]
     * @return static
     */
    public function withCookieParams(array $cookie);

    /**
     * Получить QUERY_STRINGS
     *
     * @return array
     */
    public function getQueryParams() : array;

    /**
     * Получить клон запроса с указаннымми QUERY-параметрами
     *
     * @param array $query
     * @return static
     */
    public function withQueryParams(array $query);

    /**
     * Получить параметры $_FILES
     *
     * @return array
     */
    public function getUpladedFiles() : array;

    /**
     * Получить клон запроса с указанным файлом
     *
     * @param array $upladedFiles
     * @return static
     */
    public function withUpladedFiles(array $upladedFiles);

    /**
     * Получить параметры тела запроса
     *
     * @return null|array|object
     */
    public function getParsedBody();

    /**
     * Получить клон запроса с указанным телом
     *
     * @param null|array|object $data
     * @return static
     */
    public function withParsedBody($data);

    /**
     * Получить атрибуты запроса
     *
     * @return array
     */
    public function getAttributes();

    /**
     * Получить атрибут по названию
     *
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function getAttribute($name, $default = null);

    /**
     * Получить клон запроса с указанным атрибутом
     *
     * @param string $name
     * @param mixed $value
     * @return static
     */
    public function withAttribute($name, $value);

    /**
     * Получить клон запроса без указанного атрибута
     *
     * @param string $name
     * @return static
     */
    public function withoutAttribute($name);

}