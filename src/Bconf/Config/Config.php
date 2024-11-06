<?php

namespace edrard\Bconf\Config;

use edrard\Log\MyLog;
use edrard\Bconf\Config\Drivers\IntDbDriver;
use Carbon\Carbon;

class Config
{
    protected $driver;
    protected $config;
    protected $save;
    protected $groups;
    protected $devices;
    protected $dev_conf = ['pre_command' => [''],'after_command' => [''],'command_end' => "\n","exec_type" => "write","enablePTY" => TRUE,"timeout" => 15,];

    function __construct(IntDbDriver $driver,array $config, array $save,$groups){
        $this->config = $config;
        $this->save = $save;
        $this->driver = $driver;
        $this->groups = $groups ? explode(',',$groups) : $groups;
        $this->config['time'] = Carbon::now();
        $this->loadDeviceConfig();
    }
    public function getDriver(){
        return $this->driver;
    }
    public function getSaverConfig(){
        return $this->save;
    }
    public function getConfig(){
        return $this->config;
    }
    private function loadDeviceConfig(){
        foreach (glob("src/Bconf/Config/Devices/*.php") as $filename){
            $type = basename(strtolower($filename),'.php');
            $this->devices[$type] = $this->checkDeviceConfig(include $filename);
        }
    }
    private function checkDeviceConfig(array $dev_con){
        foreach($this->dev_conf as $name => $var){
            if(!isset($dev_con[$name])){
                $dev_con[$name] = $var;
            }
        }
        return $dev_con;
    }
    public function getDevicesConfigs(){
        return $this->devices;

    }
}