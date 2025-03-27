<?php
header("Content-type: text/html; charset=utf-8");
error_reporting(E_ALL);
ini_set('display_errors', 1);
chdir('../');
if (! function_exists('xdiff_string_diff')) {
    die('Please install php-pecl-xdiff');
}

require 'vendor/autoload.php';

use Carbon\Carbon;
use edrard\Log\MyLog;
use edrard\Log\Timer;
use edrard\MyLogMail\LogInitiation;
use edrard\MyLogMail\Handlers;
use edrard\Bconf\Cli\CliConverterParser;
use edrard\Bconf\Config\Drivers\Json;


try{
    $cli = new CliConverterParser(new \Console_CommandLine());
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

    $now = Carbon::now()->toDateTimeString();

    $json = new Json($opt,[]);
    $sql = $container->get('DB');
    foreach($json->getDevices() as $devs){
        foreach($devs as $name => $dev){
            $check = (array) $sql->table('devices_config')
            ->where("name", '=', $name)->first();
            if($check === []){
                $dev['name'] = $name;
                $dev['created_at'] = $now;
                $dev['updated_at'] = $now;
                $dev = getConvert($sql,$dev,'connect',$now);
                $dev = getConvert($sql,$dev,'group',$now);
                $dev = getConvert($sql,$dev,'model',$now);
                $dev = getConvert($sql,$dev,'type',$now);
                unset($dev['group'],$dev['connect'],$dev['model'],$dev['type']);
                $sql->table('devices_config')->insert(flatten_array($dev));
                MyLog::info('Insert device '.$name.' '.$dev['ip'],[]);
            }else{
                MyLog::info('Skiping device '.$name.' '.$dev['ip'],[]);
            }
        }
    }

}Catch (\Exception $e) {
    MyLog::critical($e->getMessage(),[]);
    die($e->getMessage());
}

function getConvert($sql,$dev,$type,$now){
    $connect = $sql->table($type)
    ->where("$type", '=', $dev[$type]);
    $connect = (array) $connect->first();
    if($connect == []){
        $con = [
            "$type" => $dev[$type],
            'created_at' => $now,
            'updated_at' => $now,
        ];
        $connect['id'] = $sql->table($type)->insert($con);
    }
    $dev[$type.'_id'] = $connect['id'];
    return $dev;
}

MyLog::info("Ended in - ".Timer::getTime());