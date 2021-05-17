<?php

namespace framework\libraries\http\interfaces;

use framework\libraries\http\interfaces\StreamInterface;

interface MessageInterface {

    /**
     * Возвращает версию HTTP-протокола (например: 1.0 или 1.1)
     *
     * @return string
     */
    public function getProtocolVersion() : string;

    /**
     * Вернуть экземпляр с указанной версией протокола
     *  
     * @param string $version
     * @return static
     */
    public function withProtocolVersion(string $version);

    /**
     * Вернуть двумерный массив из заголовков
     * 
     *
     * @return array
     */
    public function getHeaders();

    /**
     * Проверить существование заголовка по имени
     *
     * @param string $name
     * @return boolean
     */
    public function hasHeader(string $name) : bool;

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
    public function getHeader(string $name);

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
    public function getHeaderLine(string $name) : string;

    /**
     * Вернуть экземпляр с указанным заголовком
     * Перезаписывает заголовок
     *
     * @param string $name
     * @param string|string[] $value
     * @return static
     * @throws \InvalidArgumentException
     */
    public function withHeader(string $name, $value);


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
    public function withAddedHeader(string $name, $value);

    /**
     * Вернуть экземпляр без указанного заголовка
     *
     * @param string $name
     * @return static
     */
    public function withoutHeader(string $name);

    /**
     * Получить тело сообщения
     *
     * @return StreamInterface
     */
    public function getBody() : StreamInterface;

    /**
     * Вернуть экземпляр с указанным телом сообщения
     *
     * @param StreamInterface $body
     * @return static
     * @throws \InvalidArgumentException
     */
    public function withBody(StreamInterface $body);
}