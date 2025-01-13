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
    protected $log_folder;
    protected $dev_conf = ['pre_command' => [''],'after_command' => [''],'command_end' => "\n","exec_type" => "write","enablePTY" => TRUE,"timeout" => 15,"command_end" => "", "config_filtets" => []];
    protected $dev_conf_telnet = ['telnet_user_prompt' => 'login:','telnet_pass_prompt' => 'password:','telnet_prompt_reg' => '\$','telnet_command_end' => "\r\n"];

    function __construct(IntDbDriver $driver,array $config, array $save,$groups, array $disable,$log_folder,array $main){
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
        $this->config['main'] = $main;
        $this->retriesCheck();
        MyLog::info("[".get_class($this)."] Main config",$main);
        $this->groups = $groups;
        $this->log_folder = $log_folder;
        $this->logFolderStatus();
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
    public function changeConfig(array $config){
        $this->config = $config;
    }
    private function retriesCheck(){
        $this->config['main']['retries'] = (int) $this->config['main']['retries'];
        $this->config['main']['retries_timeout'] = (int) $this->config['main']['retries_timeout'];
        MyLog::info("[".get_class($this)."] Retries config",$this->config['main']);
        if($this->config['main']['retries'] * $this->config['main']['retries_timeout'] > 86400){
            MyLog::info("[".get_class($this)."] You retries interval more then 1 day",$this->config['main']);
        }
    }
    private function logFolderStatus(){
        if(!$this->log_folder){
            MyLog::info("[".get_class($this)."] Not changing log folder",[]);
        }else{
            MyLog::info("[".get_class($this)."] Changing log folder",$this->log_folder);
        }
    }
    private function logGroupsStatus(){
        if($this->groups === []){
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
        foreach($this->dev_conf_telnet as $name => $var){
            if(!isset($dev_con[$name])){
                MyLog::info("[".get_class($this)."] Device telnet config ".$type." missing row ".$name,[]);
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