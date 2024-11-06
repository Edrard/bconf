<?php

namespace edrard\Bconf\Connector;

use edrard\Log\MyLog;
use edrard\Bconf\Connector\IntConnector;
use edrard\Bconf\Saver\SaveConfig;


class Connector
{
    private $driver;
    private $save_config;

    function __construct(SaveConfig $save_config){
        $this->save_config = $save_config;
    }
    function setDriver(IntConnector $driver){
        $this->driver = $driver;
    }
    public function start(){
        try{
            $this->driver->login();
            $this->driver->connect();
            $this->driver->enablePTY();
            $this->driver->setTimeouts();
            $this->driver->connect();
            $this->driver->enable();
            $this->driver->runPreCommand();
            $export = $this->driver->configExport();
            $this->driver->runAfterCommand();
            $this->save_config->saveDump($export,$this->driver->getDeviceConfig());
        }Catch (\Exception $e) {
            echo ($e->getMessage());
        }
    }
    private function enabled(){

    }
}