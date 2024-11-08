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
        if (!$this->driver->login($this->config['login'], $this->config['password'])) {
            throw new LoginException('Login failed');
        }
    }
    public function connect(){
        $this->driver->read('');
    }
    public function setTimeouts(){
        if($this->device_config['timeout']){
            $this->driver->setTimeout($this->device_config['timeout']);
        }
    }
    public function enablePTY(){
        if($this->device_config['enablePTY'] == 1){
            $this->driver->enablePTY();
        }
    }
    public function enable(){
        if($this->config['config']['enable'] == 1){
            $this->driver->write($this->config['config']['enable_command'].$this->device_config['command_end']);
            $this->driver->read($this->config['config']['enable_pass_str']); // Чекаємо запит на пароль для enable режиму
            $this->driver->write($this->config['config']['enable_pass'].$this->device_config['command_end']); // Введи свій пароль для режиму enable
            $this->driver->read($this->config['config']['search']);
        }
    }
    public function runPreCommand(){
        foreach($this->device_config['pre_command'] as $command){
            if($command){
                $this->driver->write($command.$this->device_config['command_end']);
                $this->driver->read($this->config['config']['search']);
            }
        }
    }
    public function runAfterCommand(){
        foreach($this->device_config['after_command'] as $command){
            if($command){
                $this->driver->write($command.$this->device_config['command_end']);
                $this->driver->read($this->config['config']['search']);
            }
        }
    }
    public function configExport(){
        return $this->{$this->device_config['exec_type']}();
    }
    private function exec(){
        $output = '';
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
        foreach($this->device_config['config_export'] as $command){
            if($command){
                $this->driver->write($command.$this->device_config['command_end']);
                $output .= $this->driver->read($this->config['config']['search'])."\n";
            }
        }
        return $output;
    }
}