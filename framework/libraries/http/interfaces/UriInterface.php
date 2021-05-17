<?php
/**
 * URI интерфейс согласно PSR-7
 * Все методы, которые изменяют состояние экземпляра класса 
 * должны быть реализованы таким образом, чтобы текущий экземпляр оставался таким же,
 * а эти методы возвратили новые измененные экземпляры
 */
namespace framework\libraries\http\interfaces;

interface UriInterface {

    /**
     * Получить схему запроса из URI
     *
     * @return string 
     */
    public function getScheme() : string;

    /**
     * Получить данные авторизации из URI
     *
     * @return string Формат ответа: [user-info@]host[:port]
     */
    public function getAuthority() : string;

    /**
     * Получить данные о пользователе из URI
     *
     * @return string Формат ответа: username[:password]
     */
    public function getUserInfo() : string;

    /**
     * Получить хост из URI
     *
     * @return string
     */
    public function getHost() : string;

    /**
     * Получить данные о порте из URI
     *
     * @return null|int
     */
    public function getPort() : ?int;

    /**
     * Получить данные о пути из URI
     *
     * @return string
     */
    public function getPath() : string;

    /**
     * Получить параметры запроса из URI (например, GET-параметры)
     *
     * @return string
     */
    public function getQuery() : string;

    /**
     * Получить фрагмент из URI (то, что идет после # в URI)
     *
     * @return string
     */
    public function getFragment() : string;

    /**
     * Вернуть новый объект с данной URI-схемой
     *
     * @param string $scheme
     * @return static
     * @throws \InvalidArgumentException
     */
    public function withScheme($scheme);

    /**
     * Вернуть новый объект с указанными данными пользователя
     *
     * @param string $user
     * @param null|string $password
     * @return static
     */
    public function withUserInfo($user, $password = null);

    /**
     * Вернуть новый объект URI с указанным хостом
     *
     * @param string $host
     * @return static
     * @throws \InvalidArgumentException
     */
    public function withHost($host);

    /**
     * Вернуть новый объект URI с указанным портом
     *
     * @param int|null $port
     * @return static
     * @throws \InvalidArgumentException
     */
    public function withPort($port);

    /**
     * Вернуть новый объект URI с указанным путем
     *
     * @param string $path
     * @return static
     * @throws \InvalidArgumentException
     */
    public function withPath($path);

    /**
     * Вернуть новый объект URI с указанными параметрами 
     * (например, с новыми GET-параметрами)
     *
     * @param string $query
     * @return static
     * @throws \InvalidArgumentException
     */
    public function withQuery($query);

    /**
     * Вернуть новый объект URI с указанным фрагментом
     * Например, с новым якорем
     *
     * @param string $fragment
     * @return static
     */
    public function withFragment($fragment);

    /**
     * Преобразование объекта в URI-строку
     *
     * @return string
     */
    public function __toString();
}