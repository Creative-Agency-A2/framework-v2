<?php

namespace framework\engine\container;

abstract class containerAbstract implements \framework\engine\container\containerInterface {

    protected function resolve(string $key, array $params = []){}
    protected function getDependencies(string $key, array $params){}

}