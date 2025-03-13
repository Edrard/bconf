<?php

namespace edrard\Bconf\Config\Drivers;

use edrard\Log\MyLog;
use edrard\Exc\JsonErrorException;

class Json implements IntDbDriver
{
    protected $db;
    protected $groups;
    protected $data;
    protected $tmp;
    function __construct(array $db, array $groups){
        $this->groups = $groups;
        $this->db = $db;
        try{
            $this->readDb();
        } catch (\Exception $e) {
            die($e->getMessage());
        }
    }
    public function getDevices($ammount = 10){
        $current_stat_used = 0;
        $return = [];
        foreach($this->data as $key => $val){
            MyLog::info("[".get_class($this)."] Getting devices config ".$key,[]);
            if($this->groups === [] or in_array($val['group'],$this->groups)){
                $return[$key] = $val;
                $current_stat_used++;
            }
            unset($this->data[$key]);
            if($current_stat_used == $ammount || $this->data == array()){
                $current_stat_used = 0;
                yield $return;
                $return = [];
            }
        }
    }
    public function getGroups(){
        $groups = [];
        $devices = $this->getDevices();
        foreach($devices as $devs ){
            foreach($devs as $d){
                $groups[$d['group']] = $d['group'];
            }
        }
        return $groups;
    }
    public function searchDevice(array $opt){
        if($opt['name']){
            return $this->searchByName($opt['name']);
        }
        if($opt['ip']){
            return $this->searchByIp($opt['ip']);
        }
        throw new \Exception('Please set one of the parameters, name or ip. Use -h to see how to do it');
    }
    protected function searchByIp($ip){
        if($search = array_recursive_search($ip,$this->data)){
            return [$search => $this->data[$search]] ;
        }
        throw new \Exception('Can not find device with IP '.$ip.' in database');
    }
    protected function searchByName($name){
        if(isset($this->data[$name])){
            return [$name =>$this->data[$name]];
        }
        throw new \Exception('Can not find device with name '.$name.' in database');
    }
    protected function readDb(){
        $data = file_get_contents($this->db['path']);
        MyLog::info("[".get_class($this)."] Reading Db",[]);
        $this->data = json_validate($data,true);
        if(!is_array($this->data)){
            throw new JsonErrorException($this->data,'error');
        }
        MyLog::info("[".get_class($this)."] DB data is fine",[]);
    }
    public function getConfig(){
        return [];
    }
}