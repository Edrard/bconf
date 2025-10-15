<?php

namespace edrard\Bconf;

use edrard\Log\MyLog;
use edrard\Bconf\Connector\Connector;
use edrard\Bconf\Config\Config;
use edrard\Exc\NoDeviceConfigException;
use edrard\Exc\WrongDeviceConfig;
use edrard\Exc\ConnectionProblems;
use edrard\Bconf\Config\DeviceConfigChecker;


class Starter
{
    protected $driver;
    protected $config;
    protected $groups;
    protected $connector;
    protected $cmain;
    protected $retries = [];

    function __construct(Config $config,Connector $con){
        $this->config = $config;
        $this->connector = $con;
        $this->cmain = $this->config->getConfig()['main'];
    }
    public function getDevices($devices = array()){
        MyLog::info("[".get_class($this)."] Starting dumping process",[]);
        $driver = $this->config->getDriver();
        $devices = $devices == [] ? $driver->getDevices() : $devices;
        foreach($devices as $devs ){
            $this->runBackup($devs);
        }
        $this->retries();
    }
    public function getGroups(){
        $driver = $this->config->getDriver();
        return $driver->getGroups();
    }
    private function retries(){
        if($this->retries != [] && $this->cmain['retries'] > 0){
            MyLog::info("[".get_class($this)."] Not all dumped. Retries ".$this->cmain['retries'].". Need to dump ",array_keys($this->retries));
            while(TRUE){
                $ret = $this->retries;
                $this->retries = [];
                $this->runBackup($ret);
                $this->cmain['retries']--;
                if($this->retries == []){
                    MyLog::info("[".get_class($this)."] All devices was Dumped!!!");
                    MyLog::critical("[".get_class($this)."] All devices was Dumped!!!");
                    break;
                }
                if($this->cmain['retries'] <= 0){
                    MyLog::critical("[".get_class($this)."] Cant dump next devices, retries ended",array_keys($this->retries));
                    break;
                }
                MyLog::info("[".get_class($this)."] Not all dumped, sleep next - ".$this->cmain['retries_timeout'].". Retries left ".$this->cmain['retries'].". Need to dump ",array_keys($this->retries));
                sleep($this->cmain['retries_timeout']);
            }
        }
    }
    private function runBackup($devs){
        foreach($devs as $name => $dev){
            try{
                $dev = flatten_array((array) $dev);
                $dev['name'] = isset($dev['name']) && $dev['name'] ? $dev['name'] : $name;
                $key_check = DeviceConfigChecker::check($dev);
                if($key_check !== FALSE){
                    throw new WrongDeviceConfig("Wrong device config for: ".$dev['name'].'. Cant find key - '.$key_check,'error');
                }
                $con = ucfirst($dev['connect']);
                $con_class = "edrard\\Bconf\\Connector\\$con";
                $device_config = $this->config->getDevicesConfigs();
                MyLog::info("[".get_class($this)."] Dumping device ".$dev['name'],[]);
                if(!isset($device_config[$dev['model']])){
                    throw new NoDeviceConfigException("Cant find device config for ".$dev['model'],'error');
                }
                try{
                    $connect = new $con_class($dev,$device_config[$dev['model']]);
                }Catch ( \Exception $e) {
                    throw new ConnectionProblems($e->getMessage().'. '.$dev['name'],'critical');
                }
                $this->connector->setDriver($connect);
                if($this->connector->start() === FALSE){
                    $this->retries[$dev['name']] = $dev;
                }
            }Catch (WrongDeviceConfig | NoDeviceConfigException | ConnectionProblems $e) {
                MyLog::info("[".get_class($this)."] Skiping device: ".$dev['name'],[]);
            }
        }
    }
}