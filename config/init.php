<?php
	define("DEBUG", 0);
	define("ROOT", dirname(__DIR__));
	define("WWW", ROOT . '/public');
	define("APP", ROOT . '/app');
	define("CORE", ROOT . '/app/luxury/core');
	define("LIBS", ROOT . '/app/luxury/core/libs');
	define("CACHE", ROOT . '/tmp/cache');
	define("CSV_FILES", ROOT . '/tmp/data');
	define("PROXY_FILE", ROOT . '/proxy.txt');
	define('LOG_FILE',ROOT . '/tmp/log.txt');
	define("CONF", ROOT . '/config');
	define("LAYOUT", 'default');
	
	define('BASE_SITE','https://www.carid.com');
	define('MAIN_PAGE','suspension-systems.html');
	define('USE_PROXY',0);
	define('PAGES', 5);
	
	define('SUCCESS_SCRIPT', 'Скрипт завершился: SUCCESS');
	define('FAIL_SCRIPT', 'Скрипт завершился: FAIL');
	define('FAIL_URL', 'HTTP REQUEST FAILED');
	define('FAIL_DATA', 'DATA NOT FOUND');
	
	$app_path = "http://{$_SERVER['HTTP_HOST']}{$_SERVER['PHP_SELF']}";
	$app_path = preg_replace("#[^/]+$#", "", $app_path);
	$app_path = str_replace("/public/", "", $app_path);
	
	define("PATH", $app_path);
	
	set_time_limit(0);
    ini_set('MAX_EXECUTION_TIME', 15);
	require_once ROOT . '/vendor/autoload.php';