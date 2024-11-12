<?php

namespace edrard\Bconf;

use edrard\Log\MyLog;
use edrard\Bconf\Connector\Connector;
use edrard\Bconf\Config\Config;
use edrard\Exc\NoDeviceConfigException;


class Starter
{
    protected $driver;
    protected $config;
    protected $groups;
    protected $connector;

    function __construct(Config $config,Connector $con){
        $this->config = $config;
        $this->connector = $con;
    }
    public function getDevices(){
        MyLog::info("[".get_class($this)."] Starting dumping process",[]);
        $driver = $this->config->getDriver();
        $devices = $driver->getDevices();
        foreach($devices as $devs ){
            $this->runBackup($devs);
        }
    }
    private function runBackup($devs){
        foreach($devs as $name => $dev){
            $dev['name'] = $name;
            $con = ucfirst($dev['connect']);
            $con_class = "edrard\\Bconf\\Connector\\$con";
            $device_config = $this->config->getDevicesConfigs();
            MyLog::info("[".get_class($this)."] Dumping device ".$dev['name'],$dev);
            if(!isset($device_config[$dev['model']])){
                throw new NoDeviceConfigException("Cant find device config for ".$dev['model'],'error');
            }
            $connect = new $con_class($dev,$device_config[$dev['model']]);
            $this->connector->setDriver($connect);
            $this->connector->start();
        }
    }
}