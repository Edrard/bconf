<?php

namespace edrard\Bconf\Saver;

use edrard\Log\MyLog;
use edrard\Bconf\Saver\Diff;
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
    private $base_last;

    function __construct(Filesystem $fs,Diff $diff,Config $config){
        $this->fs = $fs;
        $this->diff = $diff;
        $this->config = $config;
        $this->time = $config->getConfig()['time'];

    }
    public function saveDump($dump,$device_config){
        $this->device_config = $device_config;
        $this->path = rtrim($this->config->getSaverConfig()['path'],'/').'/'.$this->device_config['group'].'/'.$this->device_config['model'].'/'.$this->device_config['type'].'/'.$this->device_config['name'].'_'.$this->device_config['ip'];
        $this->base_last = $this->path.'/'.$this->device_config['name'].'_base.last.dump';
        $this->checkDeviceFolder();
        $this->getBaseDump($this->cleaneDump($dump));

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
    }
    protected function saveDiffDump($dump){
        $now = $this->time->format('Y-m-d');
        $year = $this->time->format('Y');
        $timestamp = $this->time->timestamp;
        $this->fs->dumpFile($this->path.'/'.$year.'/'.$this->device_config['name'].'_diff_'.$now.'_'.$timestamp.'.dump',$dump);
    }
    protected function checkDiff($new_dump,$last_dump){
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
        foreach($this->device_config['device_config']['config_export'] as $command){
            $dump = preg_replace('/'.$command.'\s*[\n]*/m', '', $dump);
        }
        $dump = str_ireplace("\x0D", "", $dump);
        $dump = preg_replace('/^[ \t]*[\r\n]+/m', '', $dump);
        $preg = preg_quote($this->device_config['config']['search']);
        $dump = preg_replace('/.*'.$preg.'.*/','',$dump);
        $tmp = preg_split('#\r?\n#', $dump, 2)[0];
        $replace = preg_replace('/.*\[9999B/', '', $tmp);
        $dump =  preg_replace('/.*'.preg_quote($replace).'.*/',$replace,$dump);
        return $dump;
    }
}