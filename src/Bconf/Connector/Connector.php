<?php

namespace edrard\Bconf\Connector;

use edrard\Log\MyLog;
use edrard\Bconf\Connector\IntConnector;
use edrard\Bconf\Saver\SaveConfig;
use edrard\Bconf\Config\Config;


class Connector
{
    private $driver;
    private $save_config;
    private $config;

    function __construct(SaveConfig $save_config, Config $config){
        $this->save_config = $save_config;
        $this->config = $config->getConfig();
        MyLog::info("[".get_class($this)."] Connector created",[]);
    }
    function setDriver(IntConnector $driver){
        $this->driver = $driver;
        MyLog::info("[".get_class($this)."] Driver was setted",[]);
    }
    public function start(){
        try{
            $export = "";
            if($this->config['disable']['dumping'] != 1){
                $this->driver->login();
                MyLog::info("[".get_class($this)."] Login",[]);
                $this->driver->connect();
                MyLog::info("[".get_class($this)."] Connect",[]);
                $this->driver->enablePTY();
                MyLog::info("[".get_class($this)."] Enable PTY",[]);
                $this->driver->setTimeouts();
                MyLog::info("[".get_class($this)."] Set timeout",[]);
                $this->driver->connect();
                $this->driver->enable();
                MyLog::info("[".get_class($this)."] Enable process",[]);
                $this->driver->runPreCommand();
                MyLog::info("[".get_class($this)."] Run pre command",[]);
                $export = $this->driver->configExport();
                MyLog::info("[".get_class($this)."] Export",[]);
                $this->driver->runAfterCommand();
                MyLog::info("[".get_class($this)."] Run after command",[]);
            }
            return $this->save_config->saveDump($export,$this->driver->getDeviceConfig());
        }Catch (\Exception $e) {
            MyLog::critical("[".get_class($this)."] Error: ".$e->getMessage(),$this->driver->getDeviceConfig());
            echo ($e->getMessage());
            return FALSE;
        }
    }
}