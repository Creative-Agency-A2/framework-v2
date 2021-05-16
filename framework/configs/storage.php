<?php

if (!defined('STORAGE_FILE_FILENAME')){
    define('STORAGE_FILE_FILENAME', __DIR_STORAGE__ . 'file-storage');
}

if (!defined('STORAGE_FILE_EXTENTION')){
    define('STORAGE_FILE_EXTENTION', 'tpm');
}

if (!defined('STORAGE_REDIS_HOST')){
    define('STORAGE_REDIS_HOST', '127.0.0.1');
}

if (!defined('STORAGE_REDIS_PORT')){
    define('STORAGE_REDIS_PORT', 6379);
}
