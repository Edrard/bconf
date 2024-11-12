<?php

namespace edrard\Bconf\Saver;

use edrard\Log\MyLog;


class Filters
{
    protected static $commands = [];
    protected static $shell_prompt = "";

    static public function setCommands($dump,array $commands = Null){
        if($commands !== Null){
            static::$commands = $commands;
        }
        return $dump;
    }
    static public function setShellPrompt($dump,$shell_prompt = Null){
        if($shell_prompt !== Null){
            static::$shell_prompt = $shell_prompt;
        }
        return $dump;
    }
    static public function removeWinNewLine($dump){
        return str_ireplace("\x0D", "", $dump);
    }
    static public function removeCommands($dump, array $commands = array()){
        if($commands === array()){
            $commands = static::$commands;
        }
        foreach($commands as $command){
            $dump = preg_replace('/'.$command.'\s*[\n]*/m', '', $dump);
        }
        return $dump;
    }
    static public function removeShellPrompt($dump,$shell_prompt = ""){
        if(!$shell_prompt){
            $shell_prompt = static::$shell_prompt;
        }
        $preg = preg_quote($shell_prompt);
        return preg_replace('/.*'.$preg.'.*/','',$dump);
    }
    static public function removeEpmtyLines($dump){
        return rtrim(preg_replace('/^[ \t]*[\r\n]+/m', '', $dump));
    }
}





#$tmp = preg_split('#\r?\n#', $dump, 2)[0];
#$replace = preg_replace('/.*\[9999B/', '', $tmp);
#$dump =  preg_replace('/.*'.preg_quote($replace).'.*/',$replace,$dump);


