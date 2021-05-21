<?php

namespace framework\libraries\http\interfaces;

use framework\libraries\http\interfaces\StreamInterface;

interface UploadedFileInterface {

    /**
     * Получить поток, соответствующий загруженному файлу
     *
     * @return StreamInterface
     * @throws \RuntimeException
     */
    public function getStream() : StreamInterface;

    /**
     * Переместить загруженный файл
     *
     * @param string $targetPath
     * @return void
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public function moveTo(string $targetPath);

    /**
     * Получить размер файла
     *
     * @return int|null
     */
    public function getSize();

    /**
     * Получить ошибку, связанную с загружаемым файлом
     * 
     * @see https://www.php.net/manual/ru/features.file-upload.errors.php
     * @return integer - код ошибки, согласно константам UPLOAD_ERR_XXX
     */
    public function getError() : int;

    /**
     * Получить название файла
     *
     * @return string|null
     */
    public function getClientFilename();

    /**
     * Получить тип файла
     *
     * @return string|null
     */
    public function getClientMediaType();

}