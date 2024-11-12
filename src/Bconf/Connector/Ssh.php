<?php

namespace edrard\Bconf\Connector;

use edrard\Log\MyLog;
use edrard\Bconf\Config\Config;
use edrard\Exc\LoginException;
use phpseclib3\Net\SSH2;


class Ssh implements IntConnector
{
    private $config = [];
    private $driver;
    private $device_config;

    function __construct(array $config,array $device_config){
        $this->config = $config;
        $this->device_config = $device_config;
        $this->driver = new SSH2($this->config['ip'],$this->config['port']);
    }
    public function getDeviceConfig(){
        $config = $this->config;
        $config['device_config'] = $this->device_config;
        return $config;
    }
    public function login(){
        MyLog::debug("[".get_class($this)."] Login to ".$this->config['ip'],);
        if (!$this->driver->login($this->config['login'], $this->config['password'])) {
            throw new LoginException('Login failed for ip '.$this->config['ip']);
        }
        MyLog::debug("[".get_class($this)."] Log in complete ",);
    }
    public function connect(){
        $con = $this->driver->read('');
        MyLog::debug("[".get_class($this)."] Connect ",[$con]);
    }
    public function setTimeouts(){
        if($this->device_config['timeout']){
            $this->driver->setTimeout($this->device_config['timeout']);
            MyLog::debug("[".get_class($this)."] Setting timeout ",[$this->device_config['timeout']]);
        }
    }
    public function enablePTY(){
        if($this->device_config['enablePTY'] == 1){
            $this->driver->enablePTY();
            MyLog::debug("[".get_class($this)."] PTY enabled ",[]);
        }
    }
    public function enable(){
        if($this->config['config']['enable'] == 1){
            MyLog::debug("[".get_class($this)."] Starting enable ",[]);
            $this->driver->write($this->config['config']['enable_command'].$this->device_config['command_end']);
            $read = $this->driver->read($this->config['config']['enable_pass_str']); // Чекаємо запит на пароль для enable режиму
            MyLog::debug("[".get_class($this)."] Read ",[$read]);
            $this->driver->write($this->config['config']['enable_pass'].$this->device_config['command_end']); // Введи свій пароль для режиму enable
            $read = $this->driver->read($this->config['config']['search']);
            MyLog::debug("[".get_class($this)."] Read ",[$read]);
        }
    }
    public function runPreCommand(){
        MyLog::debug("[".get_class($this)."] Run pre command ",[]);
        foreach($this->device_config['pre_command'] as $command){
            if($command){
                $this->driver->write($command.$this->device_config['command_end']);
                $read = $this->driver->read($this->config['config']['search']);
                MyLog::debug("[".get_class($this)."] Read ",[$read]);
            }
        }
    }
    public function runAfterCommand(){
        MyLog::debug("[".get_class($this)."] Run after command ",[]);
        foreach($this->device_config['after_command'] as $command){
            if($command){
                $this->driver->write($command.$this->device_config['command_end']);
                $read = $this->driver->read($this->config['config']['search']);
                MyLog::debug("[".get_class($this)."] Read ",[$read]);
            }
        }
    }
    public function configExport(){
        MyLog::debug("[".get_class($this)."] Run export ",[]);
        $read = $this->{$this->device_config['exec_type']}();
        MyLog::debug("[".get_class($this)."] Read ",[$read]);
        return $read;
    }
    private function exec(){
        $output = '';
        MyLog::debug("[".get_class($this)."] Exec type run ",[]);
        foreach($this->device_config['config_export'] as $command){
            if($command){
                $this->driver->exec($command.$this->device_config['command_end']);
                $output .= $this->driver->read($this->config['config']['search'])."\n";
            }
        }
        return $output;
    }
    private function write(){
        $output = '';
        MyLog::debug("[".get_class($this)."] Write type run ",[]);
        foreach($this->device_config['config_export'] as $command){
            if($command){
                $this->driver->write($command.$this->device_config['command_end']);
                $output .= $this->driver->read($this->config['config']['search'])."\n";
            }
        }
        return $output;
    }
}