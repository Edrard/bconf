<?php

header("Content-type: text/html; charset=utf-8");
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'vendor/autoload.php';

use edrard\MyLogMail\LogInitiation;
use edrard\Log\Timer;

$group = isset($argv[1])? explode(',',$argv[1]) : [];
$log_folder = isset($argv[2]) && $argv[2] ? $argv[2] : "";
$config = include "config.php";
if(isset($argv[2]) && $argv[2]){
    $config['logs']['file']['dst'] = rtrim($config['logs']['file']['dst'],'/').'/'.$argv[2];
}

new LogInitiation($config['logs']);

Timer::startTime();

$containerBuilder = new \DI\ContainerBuilder();
$containerBuilder->addDefinitions($config);
$containerBuilder->addDefinitions('structure.php');
$container = $containerBuilder->build();
$container->set('group', $group);
$container->set('log_folder', $log_folder);

return $container;