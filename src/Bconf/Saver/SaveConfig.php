<?php

namespace edrard\Bconf\Saver;

use edrard\Log\MyLog;
use edrard\Bconf\Saver\Diff;
use edrard\Bconf\Saver\Filters;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;
use edrard\Bconf\Config\Config;
use Carbon\Carbon;


class SaveConfig
{
    private $fs;
    private $diff;
    private $config;
    private $device_config;
    private $path;
    private $time;
    private $filters;
    private $base_last;

    function __construct(Filesystem $fs,Diff $diff,Config $config){
        $this->fs = $fs;
        $this->diff = $diff;
        $this->config = $config;
        $this->time = $config->getConfig()['time'];
        $this->filters = $config->getConfig()['filters'];

    }
    public function saveDump($dump,$device_config){
        $this->device_config = $device_config;
        $this->checkDump($dump);
        $this->path = rtrim($this->config->getSaverConfig()['path'],'/').'/'.$this->device_config['group'].'/'.$this->device_config['model'].'/'.$this->device_config['type'].'/'.$this->device_config['name'].'_'.$this->device_config['ip'];
        MyLog::info("[".get_class($this)."] Path to save config ".$this->path,[]);
        $this->base_last = $this->path.'/'.$this->device_config['name'].'_base.last.dump';
        $this->checkDeviceFolder();
        $this->getBaseDump($this->cleaneDump($dump));

    }
    protected function checkDump($dump){
        if(!trim($dump)){
            MyLog::warning("[".get_class($this)."] Saving empty dump for device ".$this->device_config['name'],[]);
        }
    }
    protected function getBaseDump($new_dump){
        if (! $this->fs->exists($this->base_last)) {
            $this->saveBaseLast($new_dump);
            $this->saveDiffDump($new_dump);
            return;
        }
        $last_dump = file_get_contents($this->base_last);
        $this->checkDiff($new_dump,$last_dump);
    }
    protected function saveBaseLast($dump){
        $this->fs->dumpFile($this->base_last,$dump);
        MyLog::info("[".get_class($this)."] New last dump was saved ".$this->base_last,[]);
    }
    protected function saveDiffDump($dump){
        $now = $this->time->format('Y-m-d');
        $year = $this->time->format('Y');
        $timestamp = $this->time->timestamp;
        $file = $this->path.'/'.$year.'/'.$this->device_config['name'].'_diff_'.$now.'_'.$timestamp.'.dump';
        $this->fs->dumpFile($file,$dump);
        MyLog::info("[".get_class($this)."] Diff saved to ".$file,[]);
    }
    protected function checkDiff($new_dump,$last_dump){
        MyLog::info("[".get_class($this)."] Checking diff ",[]);
        $diff = Diff::diff($this->preDiffClean($last_dump),$this->preDiffClean($new_dump));
        if($diff){
            $this->saveDiffDump($new_dump);
        }
        $this->saveBaseLast($new_dump);;
    }
    protected function checkDeviceFolder(){
        if (! $this->fs->exists($this->path)) {
            $this->fs->mkdir($this->path,0750);
        }
    }
    protected function preDiffClean($dump){
        $dump = preg_replace('/^#.*/m', '', $dump);
        return $dump;
    }
    protected function cleaneDump($dump){
        if($this->device_config['device_config']['config_filtets'] != array() &&
        is_array($this->device_config['device_config']['config_filtets']))
        {
            $this->filters = $this->device_config['device_config']['config_filtets'];
            MyLog::info("[".get_class($this)."] Custom filters setted for device ",[]);
        }

        Filters::setCommands('',$this->device_config['device_config']['config_export']);
        Filters::setShellPrompt('',$this->device_config['config']['search']);
        foreach($this->filters as $filter){
            $dump = Filters::{$filter}($dump);
            MyLog::info("[".get_class($this)."] Filter ".$filter." appling on dump",[]);
        }
        return $dump;
    }
}