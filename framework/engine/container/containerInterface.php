<?php

namespace framework\engine\container;

interface containerInterface {

    /**
     * Добавить новый класс в контейнер (название класса)
     *
     * @param string $key
     * @param mixed $value
     * @param array $params
     * @param boolean $strict - Строгий режим. Если TRUE, то не перезаписывает классы
     * @return boolean
     */
    public function set(string $key, $value = false);
    
    /**
     * Получить объект класса со всеми зависимостями
     * Все зависимые классы должны быть уже загружены
     *
     * @param string $key
     * @param array $params
     * @param boolean $singleton - этот параметр нужен для получения только одного экземпляра класса
     * @return void
     */
    public function get(string $key, array $params = [], bool $singleton = true);

    /**
     * Проверка на существование класса в контейнере
     *
     * @param string $key
     * @return boolean
     */
    public function has(string $key) : bool;

}