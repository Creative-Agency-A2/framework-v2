<?php

namespace framework\libraries\http\interfaces;

use framework\libraries\http\interfaces\MessageInterface;

interface ResponseInterface extends MessageInterface {

    /**
     * Получить код ответа
     *
     * @return integer
     */
    public function getStatusCode() : int;

    /**
     * Получить экземпляр с указанными кодом и расшифровкой ответа
     *
     * @param integer $code
     * @param string $reasonPhrase
     * @return static
     * @throws \InvalidArgumentException
     */
    public function withStatusCode(int $code, $reasonPhrase = '');

    /**
     * Получить расшифровку ответа
     *
     * @return string
     */
    public function getReasonPhrase() : string;
}