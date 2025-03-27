<?php

namespace edrard\Bconf\Config\Drivers;

use edrard\Log\MyLog;
use edrard\Bconf\Db\DbWork;

class Mysql implements IntDbDriver
{
    protected $db;
    protected $groups;
    protected $data;
    protected $tmp;
    function __construct(DbWork $db, array $groups){
        $this->db = $db;
        $this->groups = $this->getGroupsId($groups);
    }
    private function getGroupsId($groups){
        $return = [];
        if($ids = $this->db->getGroupsIdFromDb($groups)){
            foreach($ids as $id){
                $return[] = $id->id;
            }
        }
        if($return == []){
            throw new \Exception('Cant find any device groups',[]);
        }
        return $return;
    }
    public function getDevices($ammount = 10){
        $current_stat_used = 0;
        do{
            $devs = $this->db->getDevices($this->groups,$current_stat_used,$ammount);
            $current_stat_used += $ammount;
            yield $devs;
        }while(!empty($devs));
    }
    public function getGroups(){
        $groups = $this->db->getGroupsFromDb();
        if($groups == []){
            throw new \Exception('Cant find any device groups',[]);
        }
        $return = [];
        foreach($groups as $g){
            $return[$g->group] = $g->group;
        }
        return $return;
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
        if($search = $this->db->searchForDevice($ip,'ip')){
            return [$search['name'] => $search] ;
        }
        throw new \Exception('Can not find device with IP '.$ip.' in database');
    }
    protected function searchByName($name){
        if($search = $this->db->searchForDevice($name,'name')){
            return [$search['name'] => $search] ;
        }
        throw new \Exception('Can not find device with name '.$name.' in database');
    }
    public function getConfig(){
        $config = array_resort($this->db->getConfigs(),'name');

        if(isset($config['override']) && $config['override']['value'] == 1){
            $temp = [];
            foreach($config as $value){
                $temp[$value['name']] = $value['value'];
            }
            return unflatten_array($temp,'|');
        }

        return [];
    }

}