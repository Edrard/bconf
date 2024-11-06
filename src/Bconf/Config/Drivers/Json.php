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
    function __construct(array $db, string $groups){
        $this->groups = $groups ? explode(',',$groups) : $groups;
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
            if(!is_array($this->groups) or in_array($val['group'],$this->groups)){
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
    protected function readDb(){
        $data = file_get_contents($this->db['path']);
        $this->data = json_validate($data,true);
        if(!is_array($this->data)){
            throw new JsonErrorException($this->data,'error');
        }
    }
}