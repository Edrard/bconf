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
use edrard\MyLogMail\LogInitiation;

$container = include 'bootstrap.php';
Timer::startTime();

$container->set('group', isset($argv[1])? explode(',',$argv[1]) : []);
$container->set('log_folder', isset($argv[2]) && $argv[2] ? $argv[2] : "");
if(isset($argv[2]) && $argv[2]){
    $logs = $container->get('logs');
    $logs['file']['dst'] = rtrim($logs['file']['dst'],'/').'/'.$argv[2];
    $container->set('logs', $logs);
}

new LogInitiation($container->get('logs'));




try{
    $starter = $container->make('Starter');
    $starter->getDevices();
}Catch (\Exception $e) {
    MyLog::critical($e->getMessage(),[]);
    die($e->getMessage());
}
MyLog::info("Ended in - ".Timer::getTime());