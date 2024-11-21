<?php

header("Content-type: text/html; charset=utf-8");
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'vendor/autoload.php';

use edrard\MyLogMail\LogInitiation;
use edrard\Bconf\Cli\CliParser;
use edrard\Log\Timer;

$cli = new CliParser(new \Console_CommandLine());
$options = $cli->runParser();
$config = include "config.php";
if($options['groups']){
    $config['logs']['mail']['subject'] .= ' '.$options['groups'];
}
if($options['log_dir']){
    $config['logs']['file']['dst'] = rtrim($config['logs']['file']['dst'],'/').'/'.$options['log_dir'];
}


new LogInitiation($config['logs']);

Timer::startTime();

$containerBuilder = new \DI\ContainerBuilder();
$containerBuilder->addDefinitions($config);
$containerBuilder->addDefinitions('structure.php');
$container = $containerBuilder->build();
$container->set('group', $options['g']);
$container->set('log_folder', $options['log_dir']);

return $container;