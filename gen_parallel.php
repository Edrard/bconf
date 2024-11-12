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

$path = pathinfo(__FILE__);

try{
    $starter = $container->make('Starter');
    $groups = $starter->getGroups();
    if(is_array($groups) && $groups !== []){
        foreach($groups as $group){
            exec("nohup php run.php ".$group." ".$group."  > /dev/null 2>/dev/null &");
        }
    }
}Catch (\Exception $e) {
    MyLog::critical($e->getMessage(),[]);
    die($e->getMessage());
}
MyLog::info("Ended in - ".Timer::getTime());