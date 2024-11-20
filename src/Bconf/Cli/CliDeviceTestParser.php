<?php

namespace edrard\Bconf\Cli;

use Carbon\Carbon;

class CliDeviceTestParser
{
    protected $cli;

    function __construct( \Console_CommandLine $cli){
        $this->cli = $cli;
        $this->options();
    }
    protected function options(){
        $this->cli->description = 'Run device test';
        $this->cli->version = '1.0.0';
        $this->cli->addOption('name', array(
            'short_name'  => '-n',
            'long_name'   => '--name',
            'description' => 'Device name in DB',
            'action'      => 'StoreString'
        ));
        $this->cli->addOption('ip', array(
            'short_name'  => '-i',
            'long_name'   => '--ip',
            'description' => 'Device IP adress',
            'action'      => 'StoreString'
        ));
    }
    public function runParser(){
        $result = $this->cli->parse();
        if(!$result->options['name'] && !$result->options['ip']){
            throw new \Exception('Please set one of the parameters, name or ip. Use -h to see how to do it');
        }
        return $result->options;
    }

}

