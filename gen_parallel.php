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
MyLog::info('[Paralell run] Starting parallel processes',[]);
$path = pathinfo(__FILE__);

try{
    $starter = $container->make('Starter');
    $groups = $starter->getGroups();
    if(is_array($groups) && $groups !== []){
        foreach($groups as $group){
            MyLog::info('[Paralell run] Starting dump for group: '.$group,[]);
            exec("nohup php run.php -g ".$group." -l ".$group."  > /dev/null 2>/dev/null &");
        }
    }
}Catch (\Exception $e) {
    MyLog::critical('[Finish]'.$e->getMessage(),[]);
    die($e->getMessage());
}
MyLog::info("Ended in - ".Timer::getTime(),[]);