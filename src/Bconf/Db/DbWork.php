<?php

namespace edrard\Bconf\Db;

use edrard\Log\MyLog;
use edrard\DbCreate\DBNew;
use Pecee\Pixie\QueryBuilder\QueryBuilderHandler;

class DbWork
{
    protected $db = array();

    function __construct(DBNew $db){
        $this->db = $db;
    }
    public function getGroupsIdFromDb(array $groups){
        $dd = $this->db
        ->table('group');
        if($groups !== []){
            $dd->whereIn('group', $groups);
        }
        return $dd->get();
    }
    public function getDevices(array $groups,$current_stat_used,$ammount){
        return $this->db->table('devices_config')
        ->offset($current_stat_used)
        ->limit($ammount)
        ->orderBy('devices_config.id', 'desc')
        ->where('status', '=', '1')
        ->whereIn('devices_config.group',$groups)
        ->join('group', 'group.id', '=', 'devices_config.group')
        ->join('type', 'type.id', '=', 'devices_config.type')
        ->join('connect', 'connect.id', '=', 'devices_config.connect')
        ->join('model', 'model.id', '=', 'devices_config.model')
        ->get();
    }
    public function getGroupsFromDb(){
        return $this->db->table('group')
        ->get();
    }
    public function searchForDevice($search,$type){
        return (array) $this->db->table('devices_config')
        ->where('devices_config.'.$type, '=', $search)
        ->join('group', 'group.id', '=', 'devices_config.group')
        ->join('type', 'type.id', '=', 'devices_config.type')
        ->join('connect', 'connect.id', '=', 'devices_config.connect')
        ->join('model', 'model.id', '=', 'devices_config.model')
        ->first();
    }
}
