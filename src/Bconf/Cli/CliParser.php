<?php

namespace edrard\Bconf\Cli;

use Carbon\Carbon;

class CliParser
{
    protected $cli;

    function __construct( \Console_CommandLine $cli){
        $this->cli = $cli;
        $this->options();
    }
    protected function options(){
        $this->cli->description = 'Run device bconf';
        $this->cli->version = '1.0.0';
        $this->cli->addOption('groups', array(
            'short_name'  => '-g',
            'long_name'   => '--groups',
            'description' => 'Groups comma separated',
            'action'      => 'StoreString'
        ));
        $this->cli->addOption('log_dir', array(
            'short_name'  => '-l',
            'long_name'   => '--log_dir',
            'description' => 'Log directory',
            'action'      => 'StoreString'
        ));
    }
    public function runParser(){
        $result = $this->cli->parse();
        $result->options['g'] = $result->options['groups'] ? explode(',',$result->options['groups']) : [];
        $result->options['log_dir'] = $result->options['log_dir'] ? $result->options['log_dir'] : "";
        return $result->options;
    }

}

