<?php

if (!defined('__URL_PREFIX__')){
    define('__URL_PREFIX__', '');
}
if (!defined('__URL_PREFIX__')){
    define('__URL_PREFIX__', '');
}
if (!defined('__DEBUG__')){
    define('__DEBUG__', true);
}
if (!defined('__DIR_STORAGE__')){
    define('__DIR_STORAGE__', __PATH__ . '/framework/storage/');
}
if (!defined('__DIR_LIBRARIES__')){
    define('__DIR_LIBRARIES__', __PATH__ . '/framework/libraries/');
}
if (!defined('__DIR_LOGS__')){
    define('__DIR_LOGS__', __DIR_STORAGE__ . 'logs/');
}
if (!defined('__EX_LOG__')){
    define('__EX_LOG__', false);
}
if (!defined('__ERR_LOG__')){
    define('__ERR_LOG__', true);
}
if (!defined('__DB_QUERY_LOG__')){
    define('__DB_QUERY_LOG__', true);
}