<?php
header("Content-type: text/html; charset=utf-8");
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (! function_exists('xdiff_string_diff')) {
    die('Please install php-pecl-xdiff');
}

use Carbon\Carbon;
use edrard\Log\MyLog;
use edrard\Log\Timer;

$container = include 'bootstrap.php';

MyLog::init();

$container->set('group', isset($argv[1])? $argv[1] : "");

try{
    $starter = $container->make('Starter');
    $starter->getDevices();
}Catch (\Exception $e) {
    die($e->getMessage());
}