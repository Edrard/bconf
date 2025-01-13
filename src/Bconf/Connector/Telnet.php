<?php

namespace edrard\Bconf\Connector;

use edrard\Log\MyLog;
use edrard\Bconf\Config\Config;
use edrard\Exc\LoginException;
use edrard\network\Telnet as TelnetRunner;


class Telnet implements IntConnector
{
    private $config = [];
    private $driver;
    private $device_config;

    function __construct(array $config,array $device_config){
        $this->config = $config;
        $this->device_config = $device_config;
        $this->driver = new TelnetRunner($this->config['ip'],$this->config['port'],$this->device_config['timeout'],$this->device_config['timeout']);
        MyLog::debug("[".get_class($this)."] Trying to connect ",[$this->config['ip'],$this->config['port']]);
    }
    public function getDeviceConfig(){
        $config = $this->config;
        $config['device_config'] = $this->device_config;
        return $config;
    }
    public function login(){
        MyLog::debug("[".get_class($this)."] Login to ".$this->config['ip'],);
        if (!$this->driver->login($this->config['login'], $this->config['password'],'',$this->device_config['telnet_user_prompt'],$this->device_config['telnet_pass_prompt'],$this->device_config['telnet_prompt_reg'])) {
            throw new LoginException('Login failed for ip '.$this->config['ip']);
        }
        MyLog::debug("[".get_class($this)."] Log in complete ",);
    }
    public function connect(){

    }
    public function setTimeouts(){
        if($this->device_config['timeout']){
            $this->driver->setTimeout($this->device_config['timeout']);
            MyLog::debug("[".get_class($this)."] Setting timeout ",[$this->device_config['timeout']]);
        }
    }
    public function enablePTY(){

    }
    public function enable(){
        if($this->config['config_enable'] == 1){
            MyLog::debug("[".get_class($this)."] Starting enable ",[]);
            $this->driver->setPrompt($this->config['config_enable_pass_str']);
            $this->driver->exec($this->config['config_enable_command'].$this->device_config['telnet_command_end']);
            $this->driver->setPrompt($this->config['config_search']);
            $this->driver->exec($this->config['config_enable_pass'].$this->device_config['telnet_command_end']);
            MyLog::debug("[".get_class($this)."] Enable process finished ",[]);
        }
    }
    public function runPreCommand(){
        MyLog::debug("[".get_class($this)."] Run pre command ",[]);
        foreach($this->device_config['pre_command'] as $command){
            if($command){
                $this->driver->exec($command.$this->device_config['telnet_command_end']);
                MyLog::debug("[".get_class($this)."] Running Pre command ",[$command]);
            }
        }
    }
    public function runAfterCommand(){
        MyLog::debug("[".get_class($this)."] Run after command ",[]);
        foreach($this->device_config['after_command'] as $command){
            if($command){
                $this->driver->exec($command.$this->device_config['telnet_command_end']);
                MyLog::debug("[".get_class($this)."] Running After command ",[$command]);
            }
        }
    }
    public function configExport(){
        MyLog::debug("[".get_class($this)."] Run export ",[]);
        $output = '';
        foreach($this->device_config['config_export'] as $command){
            if($command){
                $output .= $this->driver->exec($command.$this->device_config['telnet_command_end']);
            }
        }
        MyLog::debug("[".get_class($this)."] Export finished ",[]);
        return $output;
    }
}