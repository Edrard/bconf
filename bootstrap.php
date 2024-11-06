<?php

header("Content-type: text/html; charset=utf-8");
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'vendor/autoload.php';

$containerBuilder = new \DI\ContainerBuilder();
$containerBuilder->addDefinitions('config.php');
$containerBuilder->addDefinitions('structure.php');
$container = $containerBuilder->build();


return $container;