<?php

namespace framework\libraries\storage;

interface StorageInterface {

    /**
     * Установить значение в хранилище
     *
     * @param string $key
     * @param mixed $value
     * @param boolean $strict
     * @return void
     */
    public function set(string $key, $value, bool $strict = false);

    /**
     * Получить значение из хранилища
     *
     * @param string $key
     * @return mixed
     */
    public function get(string $key);

    /**
     * Проверить, существет ли данные в хранилище
     *
     * @param string $key
     * @return boolean
     */
    public function has(string $key);

    /**
     * Удалить значение по ключу
     *
     * @param string $key
     * @return void
     */
    public function unset(string $key);

    /**
     * Очистить хранилище
     *
     * @return void
     */
    public function clean();

    /**
     * Получить группу значений или значение из группы
     *
     * @param string $groupKey
     * @param string $key
     * @return mixed
     */
    public function getGroup(string $groupKey, string $key = '');

    /**
     * Установить значение в группу
     *
     * @param string $groupKey
     * @param mixed $value
     * @param string $key
     * @param boolean $strict
     * @return void
     */
    public function setGroup(string $groupKey, $value, string $key = '', bool $strict = false);
}