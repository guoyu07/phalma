<?php

error_reporting(E_ALL);
ini_set('html_errors', 'on');

define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . '/Application');
define('DATA_PATH', ROOT_PATH . '/Data');

require ROOT_PATH . '/Application/Application.php';

$application = new \Application\Application();
$application->main();