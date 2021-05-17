<?php
/**
 * Класс для работы с URI
 * Согласно стандарту PSR-7
 */
namespace framework\libraries\http;

class uri implements \framework\libraries\http\interfaces\UriInterface {

    private const SCHEMES = ['http' => 80, 'https' => 443];

    private const CHAR_UNRESERVED = 'a-zA-Z0-9_\-\.~';

    private const CHAR_SUB_DELIMS = '!\$&\'\(\)\*\+,;=';

    /** 
     * Схема URI
     * 
     * @var string
     */
    private $scheme = '';

    /** 
     * Информация о пользователе
     * 
     * @var string 
     */
    private $userInfo = '';

    /**
     * Хост
     *  
     * @var string
     */
    private $host = '';

    /** 
     * Порт запроса
     * 
     * @var int|null
     */
    private $port;

    /**
     * Путь URI
     * Например, в адресе https://a--2.ru/services/ $path = services
     *  
     * @var string
     */
    private $path = '';
    
    /** 
     * Параметры URI
     * Например, в адресе https://a--2.ru/services?param=1s $query = param=1s
     * @var string 
     */
    private $query = '';

    /**
     * Фрагмент URI
     * Например, в адресе https://a--2.ru/services?param=1s#text_block $fragment = text_block 
     * @var string
     */
    private $fragment = '';

    public function __construct(string $uri)
    {
        if ('' !== $uri){
            if (false === $parts = \parse_url($uri)){
                throw new \InvalidArgumentException("Не удалось распарсить URI: $uri");
            }

            $this->scheme = isset($parts['sheme']) ? \strtolower($parts['scheme']) : '';
            $this->userInfo = $parts['user'] ?? '';
            $this->host = isset($parts['host']) ? \strtolower($parts['host']) : '';
            $this->port = isset($parts['port']) ? $this->filterPort($parts['port']) : null;
            $this->path = isset($parts['path']) ? $this->filterPath($parts['path']) : '';
            $this->query = isset($parts['query']) ? $this->filterQueryAndFragment($parts['query']) : '';
            $this->fragment = isset($parts['fragment']) ? $this->filterQueryAndFragment($parts['fragment']) : '';
            if (isset($parts['pass'])) {
                $this->userInfo .= ':' . $parts['pass'];
            }
        }
    }

    /**
     * При попытке обратиться к объекту как к строке, собираем URI обратно в строку
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->createUriString($this->scheme, $this->getAuthority(), $this->path, $this->query, $this->fragment);
    }

    /**
     * Получить схему запроса
     *
     * @return string
     */
    public function getScheme(): string
    {
        return $this->scheme;
    }

    /**
     * Получить данные авторизации
     *
     * @return string
     */
    public function getAuthority(): string
    {
        if ('' === $this->host) {
            return '';
        }

        $authority = $this->host;
        if ('' !== $this->userInfo) {
            $authority = $this->userInfo . '@' . $authority;
        }

        if (null !== $this->port) {
            $authority .= ':' . $this->port;
        }

        return $authority;
    }

    /**
     * Получить данные о пользователе
     *
     * @return string
     */
    public function getUserInfo(): string
    {
        return $this->userInfo;
    }

    /**
     * Получить информацию о хосте
     *
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * Получить порт запроса
     *
     * @return integer|null
     */
    public function getPort(): ?int
    {
        return $this->port;
    }

    /**
     * Получить путь запроса
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Получить параметры запроса
     *
     * @return string
     */
    public function getQuery(): string
    {
        return $this->query;
    }

    /**
     * Получить фрагмент запроса
     *
     * @return string
     */
    public function getFragment(): string
    {
        return $this->fragment;
    }

    /**
     * Вернуть новый объект с указанными данными пользователя
     *
     * @param string $user
     * @param null|string $password
     * @return static
     */
    public function withScheme($scheme): self
    {
        if (!\is_string($scheme)) {
            throw new \InvalidArgumentException('Схема запроса должна быть строкой');
        }

        if ($this->scheme === $scheme = \strtolower($scheme)) {
            return $this;
        }

        $new = clone $this;
        $new->scheme = $scheme;
        $new->port = $new->filterPort($new->port);

        return $new;
    }

    /**
     * Вернуть новый объект с указанными данными пользователя
     *
     * @param string $user
     * @param null|string $password
     * @return static
     */
    public function withUserInfo($user, $password = null): self
    {
        $info = $user;
        if (null !== $password && '' !== $password) {
            $info .= ':' . $password;
        }

        if ($this->userInfo === $info) {
            return $this;
        }

        $new = clone $this;
        $new->userInfo = $info;

        return $new;
    }

    /**
     * Вернуть новый объект URI с указанным хостом
     *
     * @param string $host
     * @return static
     * @throws \InvalidArgumentException
     */
    public function withHost($host): self
    {
        if (!\is_string($host)) {
            throw new \InvalidArgumentException('Хост должен быть строкой');
        }

        if ($this->host === $host = \strtolower($host)) {
            return $this;
        }

        $new = clone $this;
        $new->host = $host;

        return $new;
    }

    /**
     * Вернуть новый объект URI с указанным портом
     *
     * @param int|null $port
     * @return static
     */
    public function withPort($port): self
    {
        if ($this->port === $port = $this->filterPort($port)) {
            return $this;
        }

        $new = clone $this;
        $new->port = $port;

        return $new;
    }

    /**
     * Вернуть новый объект URI с указанным путем
     *
     * @param string $path
     * @return static
     */
    public function withPath($path): self
    {
        if ($this->path === $path = $this->filterPath($path)) {
            return $this;
        }

        $new = clone $this;
        $new->path = $path;

        return $new;
    }

    /**
     * Вернуть новый объект URI с указанными параметрами 
     * (например, с новыми GET-параметрами)
     *
     * @param string $query
     * @return static
     * @throws \InvalidArgumentException
     */
    public function withQuery($query): self
    {
        if ($this->query === $query = $this->filterQueryAndFragment($query)) {
            return $this;
        }

        $new = clone $this;
        $new->query = $query;

        return $new;
    }

    /**
     * Вернуть новый объект URI с указанным фрагментом
     * Например, с новым якорем
     *
     * @param string $fragment
     * @return static
     */
    public function withFragment($fragment): self
    {
        if ($this->fragment === $fragment = $this->filterQueryAndFragment($fragment)) {
            return $this;
        }

        $new = clone $this;
        $new->fragment = $fragment;

        return $new;
    }

    /**
     * Создание URI-строки из параметров объекта
     * @param string $scheme
     * @param string $authority
     * @param string $path
     * @param string $path
     * @param string $query
     * @param string $fragment
     * 
     * @return string
     */
    private function createUriString(string $scheme, string $authority, string $path, string $query, string $fragment): string
    {
        $uri = '';
        if ('' !== $scheme) {
            $uri .= $scheme . ':';
        }

        if ('' !== $authority) {
            $uri .= '//' . $authority;
        }

        if ('' !== $path) {
            if ('/' !== $path[0]) {
                if ('' !== $authority) {
                    $path = '/' . $path;
                }
            } elseif (isset($path[1]) && '/' === $path[1]) {
                if ('' === $authority) {
                    $path = '/' . \ltrim($path, '/');
                }
            }

            $uri .= $path;
        }

        if ('' !== $query) {
            $uri .= '?' . $query;
        }

        if ('' !== $fragment) {
            $uri .= '#' . $fragment;
        }

        return $uri;
    }

    /**
     * Проверяем, не является ли данный порт нестандартным для данной схемы
     * @param string $scheme
     * @param int $port
     * 
     * @return bool
     */
    private function isNonStandardPort(string $scheme, int $port): bool
    {
        return !isset(self::SCHEMES[$scheme]) || $port !== self::SCHEMES[$scheme];
    }

    /**
     * Проверка корректности порта
     *
     * @param null|int $port
     * @return integer|null
     */
    private function filterPort($port): ?int
    {
        if (null === $port) {
            return null;
        }

        $port = (int) $port;
        if (0 > $port || 0xffff < $port) {
            throw new \InvalidArgumentException(\sprintf('Неправильный порт: %d. Значение порта должно быть в пределах [0:65535]', $port));
        }

        return $this->isNonStandardPort($this->scheme, $port) ? $port : null;
    }

    /**
     * Проверяем путь URI на корректность
     *
     * @param mixed $path
     * @return string
     */
    private function filterPath($path): string
    {
        if (!\is_string($path)) {
            throw new \InvalidArgumentException('Путь должен быть строкой');
        }

        return \preg_replace_callback('/(?:[^' . self::CHAR_UNRESERVED . self::CHAR_SUB_DELIMS . '%:@\/]++|%(?![A-Fa-f0-9]{2}))/', [__CLASS__, 'rawurlencodeMatchZero'], $path);
    }

    /**
     * Проверяем параметры и фрагмент URI на корректность
     *
     * @param mixed $str
     * @return string
     */
    private function filterQueryAndFragment($str): string
    {
        if (!\is_string($str)) {
            throw new \InvalidArgumentException('Параметры запроса и/или фрагмент должны быть строкой');
        }

        return \preg_replace_callback('/(?:[^' . self::CHAR_UNRESERVED . self::CHAR_SUB_DELIMS . '%:@\/\?]++|%(?![A-Fa-f0-9]{2}))/', [__CLASS__, 'rawurlencodeMatchZero'], $str);
    }

    private static function rawurlencodeMatchZero(array $match): string
    {
        return \rawurlencode($match[0]);
    }
}