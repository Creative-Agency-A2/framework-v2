<?php

define('__PATH__', $_SERVER['DOCUMENT_ROOT']);
define('__DEBUG__', true);

if(!defined('__DIR_APP__')) {
    define('__DIR_APP__', __PATH__ . '/application/');
}

spl_autoload_register(function ($class_name) {
    if (!file_exists(__PATH__ . '/' . str_replace('\\', '/', $class_name) . '.php')) return 0;
    require __PATH__ . '/' . str_replace('\\', '/', $class_name) . '.php';
});

if (__DEBUG__){
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    define("__START_MEMORY__", memory_get_usage());
    define("__START_TIME__", microtime(true));
}

if (version_compare(phpversion(), '7.3.0', '<') == true) {
	exit('PHP version must be > 7.3.0');
}

if ((isset($_SERVER['HTTPS']) && (($_SERVER['HTTPS'] == 'on') || ($_SERVER['HTTPS'] == '1'))) || $_SERVER['SERVER_PORT'] == 443) {
	$_SERVER['HTTPS'] = true;
	define('__IS_HTTPS__', true);
} elseif (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' || !empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] == 'on') {
	$_SERVER['HTTPS'] = true;
	define('__IS_HTTPS__', true);
} else {
	$_SERVER['HTTPS'] = false;
	define('__IS_HTTPS__', false);
}

$framework = new \framework\engine\core();