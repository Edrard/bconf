<?php

namespace edrard\Bconf\Cli;

use Carbon\Carbon;

class CliConverterParser
{
    protected $cli;

    function __construct( \Console_CommandLine $cli){
        $this->cli = $cli;
        $this->options();
    }
    protected function options(){
        $this->cli->description = 'Convert json db to sql';
        $this->cli->version = '1.0.0';
        $this->cli->addOption('path', array(
            'short_name'  => '-f',
            'long_name'   => '--file',
            'description' => 'Full path to Json DB file name',
            'action'      => 'StoreString'
        ));
    }
    public function runParser(){
        $result = $this->cli->parse();
        if(!$result->options['path']){
            throw new \Exception('Please set path to Jsno DB file. Use -h to see how to do it');
        }
        return $result->options;
    }

}

