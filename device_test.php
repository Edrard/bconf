<?php
header("Content-type: text/html; charset=utf-8");
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (! function_exists('xdiff_string_diff')) {
    die('Please install php-pecl-xdiff');
}

require 'vendor/autoload.php';

use Carbon\Carbon;
use edrard\Log\MyLog;
use edrard\Log\Timer;
use edrard\MyLogMail\LogInitiation;
use edrard\MyLogMail\Handlers;
use edrard\Bconf\Cli\CliDeviceTestParser;

try{
    $cli = new CliDeviceTestParser(new \Console_CommandLine());
    $opt = $cli->runParser();

    $config = include "config.php";
    $config['main']['retries_timeout'] = 0;
    $config['main']['retries'] = 0;
    $config['disable']['saving'] = 1;
    $config['logs']['file']['disable'] = 0;
    $config['logs']['file']['full'] = 1;
    $config['logs']['mail']['user'] = '';

    new LogInitiation($config['logs'],'log',Handlers::stdout());

    $containerBuilder = new \DI\ContainerBuilder();
    $containerBuilder->addDefinitions($config);
    $containerBuilder->addDefinitions('structure.php');
    $container = $containerBuilder->build();
    $container->set('group', []);
    $container->set('log_folder', '');


    $config = $container->get('Config');
    $driver = $config->getDriver();
    $device = $driver->searchDevice($opt);
    $starter = $container->make('Starter');
    $starter->getDevices([$device]);
}Catch (\Exception $e) {
    MyLog::critical($e->getMessage(),[]);
    die($e->getMessage());
}
MyLog::info("Ended in - ".Timer::getTime());