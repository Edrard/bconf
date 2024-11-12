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
    protected $dev_conf = ['pre_command' => [''],'after_command' => [''],'command_end' => "\n","exec_type" => "write","enablePTY" => TRUE,"timeout" => 15,"command_end" => "", "config_filtets" => []];

    function __construct(IntDbDriver $driver,array $config, array $save,$groups, array $disable){
        $this->config = $config;
        MyLog::info("[".get_class($this)."] Db config",$this->config);
        $this->save = $save;
        MyLog::info("[".get_class($this)."] Save config",$this->save);
        $this->driver = $driver;
        MyLog::info("[".get_class($this)."] Db driver was setted",[]);
        $this->config['disable'] = $disable;
        if($this->config['disable']['dumping'] == 1){
            MyLog::warning("[".get_class($this)."] Dumping was disabled",[]);
        }
        $this->groups = $groups;
        $this->logGroupsStatus();
        $this->config['time'] = Carbon::now();
        $this->loadDeviceConfig();
        $this->getDumpFilters();
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
    private function logGroupsStatus(){
        if(!$this->groups){
            MyLog::info("[".get_class($this)."] Going to dump all groups",[]);
        }else{
            MyLog::info("[".get_class($this)."] Going to Dump next groups",$this->groups);
        }
    }
    private function loadDeviceConfig(){
        foreach (glob("src/Bconf/Config/Devices/*.php") as $filename){
            MyLog::info("[".get_class($this)."] Loading device config: ".$filename,[]);
            $type = basename(strtolower($filename),'.php');
            $this->devices[$type] = $this->checkDeviceConfig(include $filename,$type);
        }
    }
    private function checkDeviceConfig(array $dev_con,$type){
        foreach($this->dev_conf as $name => $var){
            if(!isset($dev_con[$name])){
                MyLog::info("[".get_class($this)."] Device config ".$type." missing row ".$name,[]);
                $dev_con[$name] = $var;
            }
        }
        return $dev_con;
    }
    public function getDevicesConfigs(){
        return $this->devices;

    }
    protected function getDumpFilters(){
        $class = new \ReflectionClass("edrard\\Bconf\\Saver\\Filters");
        $staticmethods = $class->getMethods(\ReflectionMethod::IS_STATIC);
        foreach($staticmethods as $filter){
            $this->config['filters'][] = $filter->name;
        }
        MyLog::info("[".get_class($this)."] Loading dump filter: ",$this->config['filters']);
    }
}